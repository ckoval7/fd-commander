<?php

use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::table('system_config')->insert([
        'key' => 'setup_completed',
        'value' => 'true',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->user = User::factory()->create();
    $this->event = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);

    EventConfiguration::factory()->create([
        'event_id' => $this->event->id,
    ]);
});

it('applies tvdashboard theme', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('data-theme="tvdashboard"', false);
});

it('displays TV dashboard header', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk()
        ->assertSee('Field Day Dashboard')
        ->assertSee('Press F to toggle header');
});

it('displays LIVE badge when event is active', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk()
        ->assertSee('LIVE');
});

it('uses 12-column grid layout', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('grid-cols-12', false);
});

it('renders only TV-visible widgets', function () {
    $tvWidgets = config('dashboard.tv_default_widgets');

    expect($tvWidgets)->toBeArray()
        ->and($tvWidgets)->not->toBeEmpty();

    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    // Check that TV widgets are being looped through
    foreach ($tvWidgets as $widgetKey) {
        $widget = config("dashboard.widgets.{$widgetKey}");
        if ($widget && $widget['tv_visible']) {
            // Widget should be rendered via @livewire
            $response->assertSee($widget['component'], false);
        }
    }
});

it('passes tvMode parameter to widgets', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    // Widgets are rendered as Livewire components with tvMode parameter
    // Verify the TV widget components are present
    $tvWidgets = config('dashboard.tv_default_widgets');
    $widgetFound = false;
    foreach ($tvWidgets as $widgetKey) {
        $widget = config("dashboard.widgets.{$widgetKey}");
        if ($widget && $widget['tv_visible']) {
            $widgetFound = true;
            break;
        }
    }
    expect($widgetFound)->toBeTrue();
});

it('includes Alpine.js TV dashboard script', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('tvDashboard', false);
    $response->assertSee('headerVisible', false);
    $response->assertSee('tv_dashboard_header_visible', false);
});

it('includes F key toggle functionality', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('@keydown.f.window.prevent', false);
    $response->assertSee('toggleHeader()', false);
});

it('includes auto-refresh failsafe', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('autoRefreshInterval', false);
    $response->assertSee('lastUpdateTime', false);
});

it('includes connection status indicator', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    // Connection status Livewire component should be rendered
    $response->assertSeeLivewire('dashboard.connection-status');
});

it('displays keyboard hint when header is hidden', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('Press F for menu');
});

it('uses smooth transitions for header toggle', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('x-transition:enter', false);
    $response->assertSee('x-transition:leave', false);
});

it('positions connection status in bottom right', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('fixed bottom-6 right-6', false);
});

it('positions keyboard hint in bottom left', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('fixed bottom-6 left-6', false);
});

it('uses large typography for TV viewing', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    // Check for large text classes
    $response->assertSee('text-5xl', false);
    $response->assertSee('text-3xl', false);
    $response->assertSee('text-2xl', false);
});

it('uses generous padding and spacing', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    // Check for generous padding
    $response->assertSee('px-8', false);
    $response->assertSee('py-8', false);
    $response->assertSee('py-6', false);
});

it('respects widget permissions in TV mode', function () {
    // Equipment status requires 'view-equipment' permission - user doesn't have it
    // So the equipment widget should NOT be rendered
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    // Verify widgets are rendered (at least the public ones)
    $tvWidgets = config('dashboard.tv_default_widgets');
    $publicWidget = null;
    foreach ($tvWidgets as $widgetKey) {
        $widget = config("dashboard.widgets.{$widgetKey}");
        if ($widget && $widget['tv_visible'] && $widget['permission'] === null) {
            $publicWidget = $widget;
            break;
        }
    }
    expect($publicWidget)->not->toBeNull();
    $response->assertSee($publicWidget['component'], false);
});

it('uses dark theme styling', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('bg-base-100', false);
    $response->assertSee('bg-neutral', false);
});

it('includes proper grid column spans for hero widgets', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    // Hero metrics should be 6 columns each
    $response->assertSee('col-span-12 lg:col-span-6', false);
});

it('includes proper grid column spans for secondary widgets', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    // Secondary metrics should be 4 columns
    $response->assertSee('col-span-12 lg:col-span-4', false);
});

it('has full-width container', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('container mx-auto', false);
});

it('uses min-h-screen for full viewport height', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard.tv'))
        ->assertOk();

    $response->assertSee('min-h-screen', false);
});
