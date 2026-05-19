<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update users table to add staff role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer', 'staff') DEFAULT 'customer'");

        // Create permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('group')->default('general'); // Group permissions by module
            $table->timestamps();
        });

        // Create user_permissions pivot table
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'permission_id']);
        });

        // Insert default permissions
        $permissions = [
            // Dashboard
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'description' => 'Access to dashboard', 'group' => 'dashboard'],
            
            // Products/Phones
            ['name' => 'view_phones', 'display_name' => 'View Phones', 'description' => 'View phone listings', 'group' => 'phones'],
            ['name' => 'create_phones', 'display_name' => 'Create Phones', 'description' => 'Add new phones', 'group' => 'phones'],
            ['name' => 'edit_phones', 'display_name' => 'Edit Phones', 'description' => 'Edit phone details', 'group' => 'phones'],
            ['name' => 'delete_phones', 'display_name' => 'Delete Phones', 'description' => 'Delete phones', 'group' => 'phones'],
            
            // Categories
            ['name' => 'view_categories', 'display_name' => 'View Categories', 'description' => 'View categories', 'group' => 'categories'],
            ['name' => 'manage_categories', 'display_name' => 'Manage Categories', 'description' => 'Create, edit, delete categories', 'group' => 'categories'],
            
            // Sales/Orders
            ['name' => 'view_sales', 'display_name' => 'View Sales', 'description' => 'View sales records', 'group' => 'sales'],
            ['name' => 'create_sales', 'display_name' => 'Create Sales', 'description' => 'Create new sales', 'group' => 'sales'],
            ['name' => 'edit_sales', 'display_name' => 'Edit Sales', 'description' => 'Edit sales records', 'group' => 'sales'],
            ['name' => 'delete_sales', 'display_name' => 'Delete Sales', 'description' => 'Delete sales records', 'group' => 'sales'],
            ['name' => 'approve_orders', 'display_name' => 'Approve Orders', 'description' => 'Approve customer orders', 'group' => 'sales'],
            
            // Reports
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'description' => 'Access to reports', 'group' => 'reports'],
            ['name' => 'export_reports', 'display_name' => 'Export Reports', 'description' => 'Export report data', 'group' => 'reports'],
            
            // Users
            ['name' => 'view_users', 'display_name' => 'View Users', 'description' => 'View user list', 'group' => 'users'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Create, edit, delete users', 'group' => 'users'],
            
            // Settings
            ['name' => 'view_settings', 'display_name' => 'View Settings', 'description' => 'View system settings', 'group' => 'settings'],
            ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'description' => 'Edit system settings', 'group' => 'settings'],
            
            // Slides
            ['name' => 'manage_slides', 'display_name' => 'Manage Slides', 'description' => 'Manage homepage slides', 'group' => 'slides'],
            
            // Delivery
            ['name' => 'manage_delivery', 'display_name' => 'Manage Delivery', 'description' => 'Manage delivery status', 'group' => 'delivery'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('permissions');
        
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer') DEFAULT 'customer'");
    }
};
