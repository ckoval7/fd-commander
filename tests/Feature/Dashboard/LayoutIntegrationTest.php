<?php

use App\Livewire\Dashboard\Layouts\DefaultLayout;
use App\Livewire\Dashboard\Layouts\TvLayout;
use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\EventType;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->eventType = EventType::create([
        'code' => 'FD',
        'name' => 'Field Day',
        'description' => 'ARRL Field Day',
        'is_active' => true,
    ]);

    $this->event = Event::factory()->create([
        'event_type_id' => $this->eventType->id,
        'start_time' => appNow()->subHours(12),
        'end_time' => appNow()->addHours(12),
    ]);

    $this->config = EventConfiguration::factory()->create([
        'event_id' => $this->event->id,
    ]);

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// ============================================================================
// DEFAULT LAYOUT TESTS
// ============================================================================

describe('DefaultLayout Component', function () {
    test('component renders successfully', function () {
        Livewire::test(DefaultLayout::class)
            ->assertOk();
    });

    test('component accepts event parameter on mount', function () {
        $component = Livewire::test(DefaultLayout::class, ['event' => $this->event]);

        expect($component->event)->not->toBeNull();
        expect($component->event->id)->toBe($this->event->id);
    });

    test('component handles null event parameter', function () {
        $component = Livewire::test(DefaultLayout::class, ['event' => null]);

        expect($component->event)->toBeNull();
    });

    test('component uses correct view template', function () {
        Livewire::test(DefaultLayout::class)
            ->assertViewIs('livewire.dashboard.layouts.default');
    });
});

// ============================================================================
// TV LAYOUT TESTS
// ============================================================================

describe('TvLayout Component', function () {
    test('component renders successfully', function () {
        Livewire::test(TvLayout::class)
            ->assertOk();
    });

    test('component accepts event parameter on mount', function () {
        $component = Livewire::test(TvLayout::class, ['event' => $this->event]);

        expect($component->event)->not->toBeNull();
        expect($component->event->id)->toBe($this->event->id);
    });

    test('component handles null event parameter', function () {
        $component = Livewire::test(TvLayout::class, ['event' => null]);

        expect($component->event)->toBeNull();
    });

    test('component uses correct view template', function () {
        Livewire::test(TvLayout::class)
            ->assertViewIs('livewire.dashboard.layouts.tv');
    });

    test('view includes tvdashboard theme attribute', function () {
        Livewire::test(TvLayout::class)
            ->assertSee('data-theme="tvdashboard"', false);
    });
});

// ============================================================================
// LAYOUT CONFIGURATION TESTS
// ============================================================================

describe('Layout Configuration', function () {
    test('dashboard config has default layout definition', function () {
        $layouts = config('dashboard.layouts');

        expect($layouts)->toHaveKey('default');
        expect($layouts['default'])->toHaveKeys(['name', 'component', 'customizable', 'description']);
        expect($layouts['default']['component'])->toBe('dashboard.layouts.default');
        expect($layouts['default']['customizable'])->toBeTrue();
    });

    test('dashboard config has tv layout definition', function () {
        $layouts = config('dashboard.layouts');

        expect($layouts)->toHaveKey('tv');
        expect($layouts['tv'])->toHaveKeys(['name', 'component', 'customizable', 'description']);
        expect($layouts['tv']['component'])->toBe('dashboard.layouts.tv');
        expect($layouts['tv']['customizable'])->toBeFalse();
    });

    test('dashboard config defines widget registry', function () {
        $widgets = config('dashboard.widgets');

        expect($widgets)->not->toBeEmpty();
        expect($widgets)->toHaveKeys([
            'qso-count',
            'score',
            'time-remaining',
            'recent-contacts',
            'band-mode-grid',
            'progress-goals',
            'equipment-status',
            'participant-stats',
            'bonus-points-manager',
            'guestbook-stats',
        ]);
    });

    test('each widget in config has required fields', function () {
        $widgets = config('dashboard.widgets');

        foreach ($widgets as $key => $widget) {
            expect($widget)->toHaveKeys([
                'name',
                'component',
                'icon',
                'permission',
                'default_visible',
                'tv_visible',
                'category',
                'size',
            ], "Widget '{$key}' missing required fields");
        }
    });

    test('widget sizes map to valid grid columns', function () {
        $gridSizes = config('dashboard.grid_sizes');

        expect($gridSizes)->toHaveKeys(['small', 'medium', 'large', 'full-width']);
        expect($gridSizes['small'])->toBe(3);
        expect($gridSizes['medium'])->toBe(4);
        expect($gridSizes['large'])->toBe(6);
        expect($gridSizes['full-width'])->toBe(12);
    });
});

// ============================================================================
// PERMISSION-BASED WIDGET FILTERING TESTS
// ============================================================================

describe('Widget Permission Filtering', function () {
    test('public widgets have null permission in config', function () {
        $publicWidgets = [
            'qso-count',
            'score',
            'time-remaining',
            'recent-contacts',
            'band-mode-grid',
            'progress-goals',
        ];

        $widgets = config('dashboard.widgets');

        foreach ($publicWidgets as $widgetKey) {
            expect($widgets[$widgetKey]['permission'])->toBeNull(
                "Widget '{$widgetKey}' should have null permission for public access"
            );
        }
    });

    test('restricted widgets have permission requirements in config', function () {
        $restrictedWidgets = [
            'equipment-status' => 'view-equipment',
            'participant-stats' => 'view-users',
            'bonus-points-manager' => 'manage-bonuses',
            'guestbook-stats' => 'manage-guestbook',
        ];

        $widgets = config('dashboard.widgets');

        foreach ($restrictedWidgets as $widgetKey => $expectedPermission) {
            expect($widgets[$widgetKey]['permission'])->toBe($expectedPermission,
                "Widget '{$widgetKey}' should require '{$expectedPermission}' permission"
            );
        }
    });

    test('user without permissions can access public widgets', function () {
        // User has no special permissions
        $widgets = config('dashboard.widgets');
        $publicWidgets = collect($widgets)->filter(fn ($widget) => $widget['permission'] === null);

        expect($publicWidgets)->not->toBeEmpty();
        expect($publicWidgets->keys()->toArray())->toContain('qso-count', 'score', 'time-remaining');
    });

    test('tv_visible flag filters widgets for TV layout', function () {
        $widgets = config('dashboard.widgets');
        $tvWidgets = collect($widgets)->filter(fn ($widget) => $widget['tv_visible'] === true);

        // TV layout should only show these widgets
        expect($tvWidgets->keys()->toArray())->toContain('qso-count', 'score', 'time-remaining');

        // TV layout should NOT show role-restricted widgets
        expect($tvWidgets->keys()->toArray())->not->toContain('equipment-status', 'guestbook-stats');
    });

    test('default_visible flag indicates initial widget visibility', function () {
        $widgets = config('dashboard.widgets');
        $defaultVisibleWidgets = collect($widgets)->filter(fn ($widget) => $widget['default_visible'] === true);

        expect($defaultVisibleWidgets)->not->toBeEmpty();
        expect($defaultVisibleWidgets->keys()->toArray())->toContain('qso-count', 'score', 'time-remaining', 'equipment-status');
    });
});

// ============================================================================
// LAYOUT RENDERING TESTS (will expand when layout views are implemented)
// ============================================================================

describe('Layout Rendering', function () {
    test('DefaultLayout component loads without errors', function () {
        $component = Livewire::test(DefaultLayout::class, ['event' => $this->event]);

        $component->assertOk();
    });

    test('TvLayout component loads without errors', function () {
        $component = Livewire::test(TvLayout::class, ['event' => $this->event]);

        $component->assertOk();
    });

    test('layouts can handle missing event gracefully', function () {
        $defaultLayout = Livewire::test(DefaultLayout::class, ['event' => null]);
        $tvLayout = Livewire::test(TvLayout::class, ['event' => null]);

        $defaultLayout->assertOk();
        $tvLayout->assertOk();
    });
});

// ============================================================================
// LAYOUT METADATA TESTS
// ============================================================================

describe('Layout Metadata', function () {
    test('default layout is customizable', function () {
        $layouts = config('dashboard.layouts');

        expect($layouts['default']['customizable'])->toBeTrue();
    });

    test('tv layout is not customizable', function () {
        $layouts = config('dashboard.layouts');

        expect($layouts['tv']['customizable'])->toBeFalse();
    });

    test('tv_default_widgets config defines fixed TV widget set', function () {
        $tvWidgets = config('dashboard.tv_default_widgets');

        expect($tvWidgets)->toBeArray();
        expect($tvWidgets)->not->toBeEmpty();
        expect($tvWidgets)->toContain('qso-count', 'score', 'time-remaining');
    });

    test('tv_default_widgets only includes tv_visible widgets', function () {
        $tvDefaultWidgets = config('dashboard.tv_default_widgets');
        $allWidgets = config('dashboard.widgets');

        foreach ($tvDefaultWidgets as $widgetKey) {
            expect($allWidgets[$widgetKey]['tv_visible'])->toBeTrue(
                "Widget '{$widgetKey}' in tv_default_widgets must have tv_visible = true"
            );
        }
    });
});
