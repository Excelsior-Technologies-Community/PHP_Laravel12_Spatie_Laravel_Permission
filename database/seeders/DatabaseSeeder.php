<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Roles & Permissions
        |--------------------------------------------------------------------------
        */

        $this->call([
            PermissionSeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Seed Sample Users
        |--------------------------------------------------------------------------
        */

        // 1. Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'System Admin', 'password' => 'password']
        );
        $admin->syncRoles(['admin']);

        // 2. Operations Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@gmail.com'],
            ['name' => 'Operations Manager', 'password' => 'password']
        );
        $manager->syncRoles(['manager']);

        // 3. Content Editor
        $editor = User::firstOrCreate(
            ['email' => 'editor@gmail.com'],
            ['name' => 'Content Editor', 'password' => 'password']
        );
        $editor->syncRoles(['editor']);

        // 4. Compliance Auditor with custom direct permission
        $auditor = User::firstOrCreate(
            ['email' => 'auditor@gmail.com'],
            ['name' => 'Compliance Auditor', 'password' => 'password']
        );
        $auditor->syncRoles(['auditor']);
        $auditor->givePermissionTo('reports.export');

        // 5. Temporary Contractor (with 24-hour expiring role)
        $contractor = User::firstOrCreate(
            ['email' => 'contractor@gmail.com'],
            ['name' => 'Temp Contractor', 'password' => 'password']
        );
        $contractor->assignRoleWithExpiry('editor', now()->addHours(24));

        // 6. Regular Demo User
        $user = User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            ['name' => 'Demo User', 'password' => 'password']
        );
        $user->syncRoles(['user']);
    }
}