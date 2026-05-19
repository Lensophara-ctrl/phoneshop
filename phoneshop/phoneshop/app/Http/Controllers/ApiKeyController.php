<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiKeyController extends Controller
{
    // Show API keys management page
    public function index()
    {
        $apiKeys = Auth::user()->apiKeys()->latest()->get();
        return view('api-keys.index', compact('apiKeys'));
    }

    // Create new API key
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $apiKey = ApiKey::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'key' => ApiKey::generateKey(),
            'permissions' => $request->permissions ?? ['*'],
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('api-keys.index')
            ->with('success', 'API key created successfully!')
            ->with('new_key', $apiKey->key);
    }

    // Revoke/Delete API key
    public function destroy(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) {
            abort(403);
        }

        $apiKey->delete();

        return redirect()->route('api-keys.index')
            ->with('success', 'API key deleted successfully!');
    }

    // Toggle API key active status
    public function toggle(ApiKey $apiKey)
    {
        if ($apiKey->user_id !== Auth::id()) {
            abort(403);
        }

        $apiKey->update(['is_active' => !$apiKey->is_active]);

        return redirect()->route('api-keys.index')
            ->with('success', 'API key status updated!');
    }
}
