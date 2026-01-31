<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates all 12 required permissions', function () {
    // Run the seeder
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    // Verify count
    expect(\Spatie\Permission\Models\Permission::count())->toBe(12);
});

it('creates permissions with correct names', function () {
    // Run the seeder
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    // Define all required permissions (matching PermissionSeeder)
    $requiredPermissions = [
        'log-contacts',
        'edit-contacts',
        'manage-events',
        'manage-event-config',
        'verify-bonuses',
        'manage-stations',
        'manage-equipment',
        'manage-users',
        'manage-roles',
        'manage-guestbook',
        'manage-images',
        'view-reports',
    ];

    // Verify each permission exists
    foreach ($requiredPermissions as $permission) {
        expect(\Spatie\Permission\Models\Permission::whereName($permission)->exists())
            ->toBeTrue("Permission '{$permission}' should exist");
    }
});
