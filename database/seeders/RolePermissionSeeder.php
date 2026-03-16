<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===== PERMISSIONS =====
        $permissions = [
            // Dashboard
            'dashboard.view',

            // User Management
            'users.view', 'users.create', 'users.edit', 'users.delete',

            // Role Management
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',

            // Category
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',

            // Product
            'products.view', 'products.create', 'products.edit', 'products.delete',

            // Supplier
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',

            // Customer
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',

            // POS / Sales
            'pos.access', 'sales.view', 'sales.create', 'sales.cancel', 'sales.refund',

            // Purchase
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.delete', 'purchases.receive',

            // Stock
            'stocks.view', 'stocks.adjust',

            // Reports
            'reports.sales', 'reports.purchases', 'reports.stock', 'reports.profit',

            // Settings
            'settings.view', 'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ===== ROLES =====

        // Super Admin - semua akses
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Manager
        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'dashboard.view',
            'categories.view', 'categories.create', 'categories.edit',
            'products.view', 'products.create', 'products.edit',
            'suppliers.view', 'suppliers.create', 'suppliers.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'pos.access', 'sales.view', 'sales.create', 'sales.cancel',
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.receive',
            'stocks.view', 'stocks.adjust',
            'reports.sales', 'reports.purchases', 'reports.stock', 'reports.profit',
        ]);

        // Kasir
        $cashier = Role::create(['name' => 'cashier']);
        $cashier->givePermissionTo([
            'dashboard.view',
            'products.view',
            'customers.view', 'customers.create',
            'pos.access', 'sales.view', 'sales.create',
        ]);

        // Gudang
        $warehouse = Role::create(['name' => 'warehouse']);
        $warehouse->givePermissionTo([
            'dashboard.view',
            'products.view',
            'suppliers.view',
            'purchases.view', 'purchases.receive',
            'stocks.view', 'stocks.adjust',
            'reports.stock',
        ]);

        // ===== DEFAULT SUPER ADMIN USER =====
        $user = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@pos.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $user->assignRole('super-admin');

        // Default Kasir
        $cashierUser = \App\Models\User::create([
            'name' => 'Kasir Demo',
            'email' => 'kasir@pos.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $cashierUser->assignRole('cashier');
    }
}