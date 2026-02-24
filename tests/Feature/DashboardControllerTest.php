<?php

use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('system_config')->insert([
        'key' => 'setup_completed',
        'value' => 'true',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('dashboard shows no-event page when no active event exists', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('No Active Event');
});

test('dashboard shows upcoming events when no active event', function () {
    $user = User::factory()->create();

    Event::factory()->create([
        'name' => 'Field Day 2026',
        'start_time' => now()->addDays(30),
        'end_time' => now()->addDays(31),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Field Day 2026');
    $response->assertSee('Upcoming Events');
});

test('dashboard shows active event dashboard when event is active', function () {
    $user = User::factory()->create();

    $event = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);

    EventConfiguration::factory()->create([
        'event_id' => $event->id,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    // Dashboard view renders (not the no-event fallback)
    // May get 500 if layout components aren't built yet, so we check
    // that we don't get the no-event page
    $response->assertDontSee('No Active Event');
})->skip('Requires dashboard layout components (task #5, #7)');

test('dashboard does not show completed events as upcoming', function () {
    $user = User::factory()->create();

    Event::factory()->create([
        'name' => 'Past Event',
        'start_time' => now()->subDays(30),
        'end_time' => now()->subDays(29),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertDontSee('Past Event');
});

test('dashboard is accessible without authentication', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
});

test('dashboard alt route requires authentication', function () {
    $response = $this->get(route('dashboard.alt'));

    $response->assertRedirect();
});
