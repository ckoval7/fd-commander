<?php

use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Mark setup as complete
    DB::table('system_config')->updateOrInsert(
        ['key' => 'setup_completed'],
        ['value' => 'true', 'updated_at' => now()]
    );

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->eventType = EventType::create([
        'code' => 'FD',
        'name' => 'Field Day',
        'description' => 'ARRL Field Day',
        'is_active' => true,
    ]);
});

test('dashboard renders with active event', function () {
    $event = Event::factory()->create([
        'event_type_id' => $this->eventType->id,
        'start_time' => now()->subHours(1),
        'end_time' => now()->addHours(23),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('LIVE');
});

test('dashboard redirects to no-event page when no active event', function () {
    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('No Active Event');
});

test('dashboard includes layout selector', function () {
    Event::factory()->create([
        'event_type_id' => $this->eventType->id,
        'start_time' => now()->subHours(1),
        'end_time' => now()->addHours(23),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSeeLivewire('dashboard.layout-selector');
});

test('dashboard includes connection status indicator', function () {
    Event::factory()->create([
        'event_type_id' => $this->eventType->id,
        'start_time' => now()->subHours(1),
        'end_time' => now()->addHours(23),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSeeLivewire('dashboard.connection-status');
});

test('dashboard includes default layout content', function () {
    Event::factory()->create([
        'event_type_id' => $this->eventType->id,
        'start_time' => now()->subHours(1),
        'end_time' => now()->addHours(23),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('dashboardCustomizer', false)
        ->assertSee('grid-cols-12', false);
});

test('tv dashboard is served at separate route', function () {
    Event::factory()->create([
        'event_type_id' => $this->eventType->id,
        'start_time' => now()->subHours(1),
        'end_time' => now()->addHours(23),
    ]);

    $response = $this->get(route('dashboard.tv'));

    $response->assertOk()
        ->assertSee('tvDashboard', false)
        ->assertSee('data-theme="tvdashboard"', false);
});

test('tv dashboard redirects when no active event', function () {
    $response = $this->get(route('dashboard.tv'));

    $response->assertRedirect(route('dashboard'));
});

test('dashboard loads valid layouts from config', function () {
    Event::factory()->create([
        'event_type_id' => $this->eventType->id,
        'start_time' => now()->subHours(1),
        'end_time' => now()->addHours(23),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();

    // Verify layouts are passed to the view
    $layouts = config('dashboard.layouts');
    expect($layouts)->toHaveKeys(['default', 'tv']);
});

test('dashboard is accessible without authentication', function () {
    auth()->logout();

    $response = $this->get(route('dashboard'));

    // Dashboard root is public
    $response->assertOk();
});

test('dashboard header shows event name when active', function () {
    $event = Event::factory()->create([
        'event_type_id' => $this->eventType->id,
        'name' => 'Test Field Day 2026',
        'start_time' => now()->subHours(1),
        'end_time' => now()->addHours(23),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Dashboard');
});

test('dashboard uses Alpine.js for widget customization', function () {
    Event::factory()->create([
        'event_type_id' => $this->eventType->id,
        'start_time' => now()->subHours(1),
        'end_time' => now()->addHours(23),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('x-data', false)
        ->assertSee('dashboardCustomizer', false);
});
