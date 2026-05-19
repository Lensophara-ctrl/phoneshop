# Staff User Restrictions

## Overview
This document outlines user management restrictions for both staff and admin users, with special protections for admin accounts.

## Admin User Protections (Applied to ALL Users)

### 1. Admin Users Cannot Be Deleted
- **NO ONE** can delete admin users (not even other admins)
- This is a security feature to prevent accidental or malicious deletion of admin accounts
- Delete button is completely hidden for all admin users
- Shows "Protected" badge with shield icon instead
- Backend validation prevents any deletion attempts

### 2. Staff Restrictions on Admin Users

Staff members additionally CANNOT:
- Edit admin user profiles
- Reset admin passwords
- Create new admin users
- Promote any user to admin role

## Staff-Specific Restrictions

### 2. Cannot Edit Admin Users
- Staff members cannot edit admin user profiles
- Edit button is hidden for admin users
- Attempting to access edit page redirects with error message

### 3. Cannot Reset Admin Passwords
- Staff members cannot reset passwords for admin users
- Reset password button is hidden for admin users
- Backend validation prevents password reset attempts

### 4. Cannot Create Admin Users
- Staff members can only create other staff users or customers
- Admin role option is hidden in the create user form
- Backend validation prevents admin user creation

### 5. Cannot Promote Users to Admin
- Staff members cannot change any user's role to 'admin'
- Admin option is hidden/disabled in role dropdown when staff is editing
- Backend validation prevents role changes to admin

## Additional Protections

### Self-Deletion Prevention
- No user (admin or staff) can delete their own account
- Prevents accidental lockouts

### Admin Account Deletion Prevention
- Admin accounts are permanently protected from deletion
- This ensures system stability and prevents unauthorized admin removal
- If an admin account needs to be removed, it must be done directly in the database

## UI Indicators

When viewing the user list:
- Admin users show a "Protected" badge with shield icon instead of delete button
- This applies to both admin and staff viewers
- Edit and reset password buttons are still available for admins viewing other admins
- Staff users see limited actions for admin accounts

## Error Messages

Users attempting restricted actions will see:
- "Admin users cannot be deleted for security reasons" (applies to everyone)
- "Staff members cannot edit admin users" (staff only)
- "Staff members cannot reset admin passwords" (staff only)
- "Staff members cannot create admin users" (staff only)
- "Staff members cannot create or promote users to admin role" (staff only)
- "You cannot delete your own account" (applies to everyone)

## What Each Role CAN Do

### Admin Users Can:
- ✅ View all users
- ✅ Create admin, staff, and customer users
- ✅ Edit all users (including other admins)
- ✅ Delete staff and customer users
- ❌ Delete admin users (protected for security)
- ✅ Reset passwords for all users
- ✅ Assign permissions to staff users
- ✅ Change user roles (including promoting to admin)

### Staff Users Can:
- ✅ View all users (including admins)
- ✅ Create staff users
- ✅ Create customer users
- ✅ Edit staff users
- ✅ Edit customer users
- ✅ Delete staff users (except themselves)
- ✅ Delete customer users
- ❌ Delete admin users (protected)
- ✅ Reset passwords for staff and customer users
- ❌ Reset passwords for admin users
- ✅ Assign permissions to staff users
- ❌ Create or promote users to admin role

## Implementation Details

### Controller Level Protection
User deletion in `UserController` checks:
```php
// Prevent deleting your own account
if ($user->id === auth()->id()) {
    return back()->with('error', 'You cannot delete your own account');
}

// Prevent anyone (including admins) from deleting admin users
if ($user->role === 'admin') {
    return back()->with('error', 'Admin users cannot be deleted for security reasons');
}
```

Staff restrictions for editing/password reset:
```php
if (auth()->user()->role === 'staff' && $user->role === 'admin') {
    return redirect()->route('users.index')->with('error', 'Staff members cannot [action] admin users');
}
```

### View Level Protection
Blade templates conditionally show/hide buttons:
```blade
{{-- Delete button only for non-admin users --}}
@if($user->role !== 'admin' && $user->id !== auth()->id())
    <!-- Show delete button -->
@elseif($user->role === 'admin')
    <!-- Show protected badge -->
@endif

{{-- Edit/Reset buttons hidden for staff viewing admins --}}
@if(auth()->user()->role === 'admin' || $user->role !== 'admin')
    <!-- Show edit and reset password buttons -->
@endif
```

### Form Level Protection
Role selection dropdowns hide admin option for staff:
```blade
@if(auth()->user()->role === 'admin')
    <option value="admin">Admin</option>
@endif
```

## Testing

### Test Admin Protections:
1. Login as admin user: `admin@gmail.com` / `admin123`
2. Navigate to User Management
3. Try to delete another admin user
4. Verify deletion is blocked with "Protected" badge and error message

### Test Staff Restrictions:
1. Login as staff user: `staff@gmail.com` / `staff123`
2. Navigate to User Management
3. Try to edit/delete/reset password for admin users
4. Try to create a new admin user
5. Verify all actions are blocked with appropriate error messages

## Security Notes

- Admin accounts are protected from deletion by anyone
- All restrictions are enforced at both UI and backend levels
- Staff users cannot bypass restrictions through direct URL access
- Form submissions are validated server-side
- Error messages are user-friendly but don't expose system details
- If an admin account truly needs to be removed, it must be done via database access
