<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('isSystemAdmin returns true for system admin role', function () {
    // Create the system-admin role
    Role::create(['name' => 'system-admin']);

    $admin = User::factory()->create();
    $admin->assignRole('system-admin');
    expect($admin->isSystemAdmin())->toBeTrue();

    $user = User::factory()->create();
    expect($user->isSystemAdmin())->toBeFalse();
});

test('isLocked returns true when account is locked', function () {
    $locked = User::factory()->create(['account_locked_at' => now()->addHour()]);
    expect($locked->isLocked())->toBeTrue();

    $unlocked = User::factory()->create(['account_locked_at' => now()->subHour()]);
    expect($unlocked->isLocked())->toBeFalse();

    $never = User::factory()->create(['account_locked_at' => null]);
    expect($never->isLocked())->toBeFalse();
});

test('has2FAEnabled returns true when two factor is enabled', function () {
    $enabled = User::factory()->create(['two_factor_secret' => encrypt('test-secret')]);
    expect($enabled->has2FAEnabled())->toBeTrue();

    $disabled = User::factory()->create(['two_factor_secret' => null]);
    expect($disabled->has2FAEnabled())->toBeFalse();
});
