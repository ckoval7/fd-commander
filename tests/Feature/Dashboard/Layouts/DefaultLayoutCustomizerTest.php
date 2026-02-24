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

it('displays the customizer toggle button', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Customize Widgets');
});

it('displays widget categories in customizer', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Scoring')
        ->assertSee('Event')
        ->assertSee('Activity')
        ->assertSee('Analytics');
});

it('displays all public widgets in customizer', function () {
    $widgets = collect(config('dashboard.widgets'))
        ->filter(fn ($widget) => $widget['permission'] === null);

    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    foreach ($widgets as $widget) {
        $response->assertSee($widget['name']);
    }
});

it('hides permission-restricted widget checkboxes from users without permission', function () {
    // Equipment status requires 'view-equipment' permission
    // The widget name may appear in JavaScript config but the checkbox should not be rendered
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    // The widget checkbox label should not be rendered for users without permission
    // Check that the specific widget checkbox is not in the HTML form
    $content = $response->content();

    // The equipment-status widget should not have a rendered checkbox label in the customizer
    expect($content)->not->toContain('value="equipment-status"');
});

it('displays reset to defaults button', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Reset to Defaults');
});

it('displays widget size information', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Size: Medium')
        ->assertSee('Size: Small')
        ->assertSee('Size: Large');
});

it('displays empty state message', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('No Widgets Selected');
});

it('includes Alpine.js dashboard customizer script', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    $response->assertSee('dashboardCustomizer', false);
    $response->assertSee('customizerOpen', false);
    $response->assertSee('visibleWidgets', false);
    $response->assertSee('dashboard_widget_prefs', false);
});

it('loads default widget order from config', function () {
    $defaultOrder = config('dashboard.default_widget_order');

    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    // The default order is passed via @js() which includes the widget keys
    $response->assertSee('defaultOrder', false);
    $response->assertSee('loadDefaults', false);
});

it('includes widget configuration in JavaScript', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    // Check that widget config is passed to Alpine
    $response->assertSee('widgets[widgetKey]', false);
});

it('includes grid size configuration in JavaScript', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    // Check that grid sizes are used in the customizer
    $response->assertSee('gridSizes', false);
    $response->assertSee('getWidgetGridClass', false);
});

it('displays persistence information', function () {
    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Your preferences are saved automatically and persist across sessions');
});

it('uses x-cloak to prevent flash of unstyled content', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    $response->assertSee('x-cloak', false);
});

it('includes smooth transitions for widget visibility', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    $response->assertSee('x-transition', false);
});

it('displays checkboxes for each widget', function () {
    $publicWidgets = collect(config('dashboard.widgets'))
        ->filter(fn ($widget) => $widget['permission'] === null)
        ->count();

    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    // Each widget should have a checkbox input
    expect(substr_count($response->content(), 'type="checkbox"'))
        ->toBeGreaterThanOrEqual($publicWidgets);
});

it('groups widgets by category', function () {
    $categories = config('dashboard.categories');
    $widgets = config('dashboard.widgets');

    // Only check categories that have public (no permission) widgets
    $visibleCategories = collect($widgets)
        ->filter(fn ($widget) => $widget['permission'] === null)
        ->pluck('category')
        ->unique()
        ->map(fn ($key) => $categories[$key] ?? null)
        ->filter()
        ->values();

    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    foreach ($visibleCategories as $categoryName) {
        $response->assertSee($categoryName);
    }
});

it('displays widget icon markup', function () {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk();

    // Icon components render as SVG elements
    $content = $response->content();
    expect($content)->toContain('<svg');
});
