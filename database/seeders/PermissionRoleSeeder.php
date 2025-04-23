<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $this->createPermissions();

        // Create roles and assign permissions
        $this->createRolesWithPermissions();

        $this->command->info('Permissions and Roles have been created successfully!');
    }

    /**
     * Create all required permissions
     */
    protected function createPermissions(): void
    {
        $permissions = $this->getPermissions();

        $count = 0;
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            $count++;
        }

        $this->command->info("Created {$count} permissions");
    }

    /**
     * Create roles with their respective permissions
     */
    protected function createRolesWithPermissions(): void
    {
        $allPermissions = Permission::all();

        // Super Admin role
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($allPermissions);
        $this->command->info("Created Super Admin role with all permissions");

        // Admin role
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminPermissions = Permission::whereNotIn('name', [
            'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role'
        ])->get();
        $admin->syncPermissions($adminPermissions);
        $this->command->info("Created Admin role with " . $adminPermissions->count() . " permissions");

        // Cashier role
        $cashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashierPermissions = [
            // POS related permissions
            'access_pos',

            // Products
            'view_any_product',
            'view_product',

            // Categories
            'view_any_category',
            'view_category',

            // Payment methods
            'view_any_payment::method',
            'view_payment::method',

            // Toppings
            'view_any_topping',
            'view_topping',

            // Sizes
            'view_any_size',
            'view_size',

            // Discounts
            'view_any_discount',
            'view_discount',
        ];

        $cashier->syncPermissions($cashierPermissions);
        $this->command->info("Created Cashier role with " . count($cashierPermissions) . " permissions");
    }

    /**
     * Define all permissions needed for the application
     */
    protected function getPermissions(): array
    {
        return [
            // User permissions
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'restore_user',
            'replicate_user',

            // Role permissions
            'view_any_role',
            'view_role',
            'create_role',
            'update_role',
            'delete_role',

            // Category permissions
            'view_any_category',
            'view_category',
            'create_category',
            'update_category',
            'delete_category',
            'restore_category',
            'replicate_category',

            // Product permissions
            'view_any_product',
            'view_product',
            'create_product',
            'update_product',
            'delete_product',
            'restore_product',
            'replicate_product',

            // Size permissions
            'view_any_size',
            'view_size',
            'create_size',
            'update_size',
            'delete_size',
            'restore_size',
            'replicate_size',

            // Topping permissions
            'view_any_topping',
            'view_topping',
            'create_topping',
            'update_topping',
            'delete_topping',
            'restore_topping',
            'replicate_topping',

            // Payment Method permissions
            'view_any_payment::method',
            'view_payment::method',
            'create_payment::method',
            'update_payment::method',
            'delete_payment::method',
            'restore_payment::method',
            'replicate_payment::method',

            // Discount permissions
            'view_any_discount',
            'view_discount',
            'create_discount',
            'update_discount',
            'delete_discount',
            'restore_discount',
            'replicate_discount',

            'access_pos',
            'view_reports',
            'export_reports',

            // Custom permissions
            'order_overview',
        ];
    }
}
