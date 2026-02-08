<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns call_sign when dev mode is disabled', function () {
    config(['developer.enabled' => false]);
    $user = User::factory()->create(['call_sign' => 'W1AW']);
    expect($user->effectiveCallSign())->toBe('W1AW');
});

it('returns callsign session override when dev mode is enabled', function () {
    config(['developer.enabled' => true]);
    session(['dev_callsign_override' => 'K2ABC']);
    $user = User::factory()->create(['call_sign' => 'W1AW']);
    expect($user->effectiveCallSign())->toBe('K2ABC');
});

it('returns call_sign when dev mode is enabled but no session override', function () {
    config(['developer.enabled' => true]);
    $user = User::factory()->create(['call_sign' => 'W1AW']);
    expect($user->effectiveCallSign())->toBe('W1AW');
});

it('ignores callsign override when dev mode is off', function () {
    config(['developer.enabled' => false]);
    session(['dev_callsign_override' => 'K2ABC']);
    $user = User::factory()->create(['call_sign' => 'W1AW']);
    expect($user->effectiveCallSign())->toBe('W1AW');
});
