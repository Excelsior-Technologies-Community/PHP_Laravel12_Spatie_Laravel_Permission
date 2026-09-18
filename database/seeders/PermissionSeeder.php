<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Seed application roles and permissions.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Users module
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Roles module
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Permissions module
            'permissions.view',
            'permissions.create',
            'permissions.delete',

            // Orders module
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.refund',

            // Invoices module
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'invoices.export',

            // Articles module
            'articles.view',
            'articles.create',
            'articles.edit',
            'articles.delete',
            'articles.publish',

            // Reports module
            'reports.view',
            'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles Setup
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $auditor = Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Admin gets ALL permissions
        $admin->syncPermissions(Permission::all());

        // Manager permissions
        $manager->syncPermissions([
            'users.view',
            'orders.view',
            'orders.create',
            'orders.edit',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.export',
            'reports.view',
            'reports.export',
        ]);

        // Editor permissions
        $editor->syncPermissions([
            'articles.view',
            'articles.create',
            'articles.edit',
            'articles.delete',
            'articles.publish',
        ]);

        // Auditor permissions
        $auditor->syncPermissions([
            'users.view',
            'orders.view',
            'invoices.view',
            'reports.view',
        ]);

        // User (regular member) permissions
        $user->syncPermissions([
            'users.view',
            'articles.view',
        ]);
    }
}