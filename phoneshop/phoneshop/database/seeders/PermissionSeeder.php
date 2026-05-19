<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions are already created in migration
        // This seeder is for creating a sample staff user with permissions
        
        // Create a staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('staff123'),
                'role' => 'staff',
            ]
        );

        // Assign some permissions to staff
        $permissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_phones',
            'create_phones',
            'edit_phones',
            'view_categories',
            'view_sales',
            'create_sales',
            'view_reports',
            'manage_chat',
        ])->pluck('id')->toArray();

        $staff->permissions()->sync($permissions);

        $this->command->info('Staff user created with permissions!');
        $this->command->info('Email: staff@gmail.com');
        $this->command->info('Password: staff123');
    }
}
