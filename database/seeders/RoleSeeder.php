<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Clear permission cache to ensure all permissions are available
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // System Administrator - All except contact logging
        $systemAdmin = Role::create([
            'name' => 'System Administrator',
            'guard_name' => 'web',
        ]);
        $systemAdmin->givePermissionTo(
            Permission::whereNotIn('name', ['log-contacts', 'edit-contacts'])->pluck('name')
        );

        // Event Manager
        $eventManager = Role::create([
            'name' => 'Event Manager',
            'guard_name' => 'web',
        ]);
        $eventManager->givePermissionTo([
            'view-events',
            'create-events',
            'edit-events',
            'delete-events',
            'activate-events',
            'manage-events',
            'manage-event-config',
            'manage-stations',
            'manage-equipment',
            'verify-bonuses',
            'view-reports',
            'manage-guestbook',
            'manage-images',
            'view-security-logs',
        ]);

        // Operator (Default)
        $operator = Role::create([
            'name' => 'Operator',
            'guard_name' => 'web',
        ]);
        $operator->givePermissionTo('log-contacts');

        // Station Captain
        $stationCaptain = Role::create([
            'name' => 'Station Captain',
            'guard_name' => 'web',
        ]);
        $stationCaptain->givePermissionTo([
            'log-contacts',
            'edit-contacts',
            'manage-stations',
            'manage-equipment',
        ]);

        $this->command->info('Created 4 roles with permissions');
    }
}
