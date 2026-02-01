<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Contact Logging
            ['name' => 'log-contacts', 'description' => 'Create new QSO entries'],
            ['name' => 'edit-contacts', 'description' => 'Edit or delete existing QSOs'],

            // Event Management
            ['name' => 'manage-events', 'description' => 'Create and edit Field Day events'],
            ['name' => 'manage-event-config', 'description' => 'Configure event settings'],
            ['name' => 'verify-bonuses', 'description' => 'Approve or reject bonus point claims'],

            // Station & Equipment
            ['name' => 'manage-stations', 'description' => 'Add and edit operating stations'],
            ['name' => 'manage-equipment', 'description' => 'Manage equipment inventory'],

            // User Administration
            ['name' => 'manage-users', 'description' => 'Create, edit, and delete user accounts'],
            ['name' => 'manage-roles', 'description' => 'Create roles and assign permissions'],
            ['name' => 'manage-settings', 'description' => 'Configure system settings and preferences'],

            // Content Management
            ['name' => 'manage-guestbook', 'description' => 'Moderate visitor guestbook entries'],
            ['name' => 'manage-images', 'description' => 'Upload and delete event photos'],

            // Reporting
            ['name' => 'view-reports', 'description' => 'Access detailed score reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        $this->command->info('Created 13 permissions');
    }
}
