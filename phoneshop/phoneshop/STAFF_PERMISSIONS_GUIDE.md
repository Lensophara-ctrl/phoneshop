# Staff Permissions & Role Management Guide

## Overview
This system implements a comprehensive role-based permission system with three user roles:
- **Admin**: Full access to all features (no permission checks needed)
- **Staff**: Limited access based on assigned permissions
- **Customer**: Frontend shopping access only

## Features Implemented

### 1. Database Structure
- `permissions` table: Stores all available permissions
- `user_permissions` pivot table: Links users to their permissions
- Updated `users` table: Added 'staff' to role enum

### 2. Models & Relationships
- **Permission Model**: Manages permission records
- **User Model**: Extended with permission methods:
  - `hasPermission($permission)`: Check single permission
  - `hasAnyPermission($permissions)`: Check if user has any of the permissions
  - `hasAllPermissions($permissions)`: Check if user has all permissions
  - `givePermission($permission)`: Assign permission
  - `revokePermission($permission)`: Remove permission
  - `syncPermissions($permissions)`: Sync all permissions

### 3. Middleware
- **AdminMiddleware**: Only allows admin role
- **StaffMiddleware**: Allows both admin and staff roles
- **PermissionMiddleware**: Checks specific permission (e.g., `middleware('permission:view_sales')`)

### 4. Available Permissions (Grouped by Module)

#### Dashboard
- `view_dashboard`: Access to dashboard

#### Products/Phones
- `view_phones`: View phone listings
- `create_phones`: Add new phones
- `edit_phones`: Edit phone details
- `delete_phones`: Delete phones

#### Categories
- `view_categories`: View categories
- `manage_categories`: Create, edit, delete categories

#### Sales/Orders
- `view_sales`: View sales records
- `create_sales`: Create new sales
- `edit_sales`: Edit sales records
- `delete_sales`: Delete sales records
- `approve_orders`: Approve customer orders

#### Reports
- `view_reports`: Access to reports
- `export_reports`: Export report data

#### Users
- `view_users`: View user list
- `manage_users`: Create, edit, delete users

#### Settings
- `view_settings`: View system settings
- `manage_settings`: Edit system settings

#### Slides
- `manage_slides`: Manage homepage slides

#### Delivery
- `manage_delivery`: Manage delivery status

## Usage Examples

### In Controllers
```php
// Check permission in controller
if (!auth()->user()->hasPermission('create_phones')) {
    return redirect()->back()->with('error', 'Unauthorized');
}

// Check multiple permissions
if (auth()->user()->hasAnyPermission(['edit_phones', 'delete_phones'])) {
    // Allow action
}
```

### In Routes
```php
// Require specific permission
Route::get('/phones', [PhoneController::class, 'index'])
    ->middleware('permission:view_phones');

// Allow admin and staff
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('staff');
```

### In Blade Templates
```blade
@permission('create_phones')
    <a href="{{ route('phones.create') }}" class="btn btn-primary">Add Phone</a>
@endpermission

@anypermission('edit_phones', 'delete_phones')
    <div class="admin-actions">...</div>
@endanypermission

@role('admin')
    <div class="admin-only-section">...</div>
@endrole
```

## Test Credentials

### Admin User
- Email: `admin@gmail.com`
- Password: `admin123`
- Access: Full system access

### Staff User (Sample)
- Email: `staff@gmail.com`
- Password: `staff123`
- Permissions: Dashboard, View/Create/Edit Phones, View Categories, View/Create Sales, View Reports

## Creating New Staff Users

1. Login as admin
2. Go to User Management
3. Click "Create New User"
4. Select "Staff" role
5. Check the permissions you want to assign
6. Fill in user details and submit

## Editing User Permissions

1. Go to User Management
2. Click the edit icon (pen) next to a user
3. Change role or modify permissions
4. Save changes

## Adding New Permissions

To add new permissions, create a migration:

```php
DB::table('permissions')->insert([
    'name' => 'permission_name',
    'display_name' => 'Display Name',
    'description' => 'Description of what this allows',
    'group' => 'module_name',
    'created_at' => now(),
    'updated_at' => now(),
]);
```

## Security Notes

- Admins always have all permissions (no checks needed)
- Staff users must be explicitly granted permissions
- Customers have no backend permissions
- Permission checks happen at middleware and controller level
- Blade directives provide UI-level permission checks

## Files Modified/Created

### New Files
- `app/Models/Permission.php`
- `app/Http/Middleware/StaffMiddleware.php`
- `app/Http/Middleware/PermissionMiddleware.php`
- `database/migrations/2026_04_11_000000_add_staff_role_and_create_permissions_tables.php`
- `database/seeders/PermissionSeeder.php`
- `resources/views/users/edit.blade.php`

### Modified Files
- `app/Models/User.php` - Added permission methods
- `app/Http/Controllers/UserController.php` - Added edit/update methods
- `app/Providers/AppServiceProvider.php` - Added Blade directives
- `bootstrap/app.php` - Registered middleware
- `routes/web.php` - Added edit/update routes
- `resources/views/users/index.blade.php` - Added staff badge and edit button
- `resources/views/users/create-admin.blade.php` - Added role selection and permissions

## Next Steps

To protect specific routes with permissions:

1. Update route middleware:
```php
Route::resource('phones', PhoneController::class)
    ->middleware('permission:view_phones');
```

2. Or check in controller:
```php
public function index()
{
    if (!auth()->user()->hasPermission('view_phones')) {
        abort(403, 'Unauthorized');
    }
    // ... rest of code
}
```

3. Hide UI elements:
```blade
@permission('create_phones')
    <button>Create Phone</button>
@endpermission
```
