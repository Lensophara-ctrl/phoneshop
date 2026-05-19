<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    public function createAdmin()
    {
        return view('users.create-admin');
    }

    public function storeAdmin(Request $request)
    {
        // Prevent staff from creating admin users
        if (auth()->user()->role === 'staff' && $request->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Staff members cannot create admin users');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,staff'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profiles', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'profile_image' => $profileImagePath,
        ]);

        // Assign permissions if role is staff
        if ($request->role === 'staff' && $request->has('permissions')) {
            $user->permissions()->sync($request->permissions);
        }

        $roleLabel = $request->role === 'admin' ? 'Admin' : 'Staff';
        return redirect()->route('users.index')->with('success', "{$roleLabel} user created successfully");
    }

    public function resetPassword(User $user)
    {
        // Prevent staff from resetting admin passwords
        if (auth()->user()->role === 'staff' && $user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Staff members cannot reset admin passwords');
        }

        return view('users.reset-password', compact('user'));
    }

    public function updatePassword(Request $request, User $user)
    {
        // Prevent staff from updating admin passwords
        if (auth()->user()->role === 'staff' && $user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Staff members cannot reset admin passwords');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', "Password reset for {$user->name}");
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Prevent staff from editing admin users
        if (auth()->user()->role === 'staff' && $user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Staff members cannot edit admin users');
        }

        $permissions = \App\Models\Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        return view('users.edit', compact('user', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        // Prevent staff from updating admin users
        if (auth()->user()->role === 'staff' && $user->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Staff members cannot edit admin users');
        }

        // Prevent staff from changing their own role or others to admin
        if (auth()->user()->role === 'staff' && $request->role === 'admin') {
            return redirect()->route('users.index')->with('error', 'Staff members cannot create or promote users to admin role');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,staff,customer'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $user->update($data);

        // Sync permissions for staff users
        if ($request->role === 'staff') {
            $user->permissions()->sync($request->permissions ?? []);
        } else {
            // Clear permissions for non-staff users
            $user->permissions()->detach();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Prevent deleting your own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account');
        }

        // Prevent anyone (including admins) from deleting admin users
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin users cannot be deleted for security reasons');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully');
    }
}
