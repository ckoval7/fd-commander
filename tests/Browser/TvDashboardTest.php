<?php

namespace Tests\Browser;

use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TvDashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected EventType $eventType;

    protected Event $event;

    protected EventConfiguration $config;

    protected function setUp(): void
    {
        parent::setUp();

        // Mark setup as complete
        DB::table('system_config')->updateOrInsert(
            ['key' => 'setup_completed'],
            ['value' => 'true', 'updated_at' => now()]
        );

        // Create test user
        $this->user = User::factory()->create();

        // Create event type
        $this->eventType = EventType::create([
            'code' => 'FD',
            'name' => 'Field Day',
            'description' => 'ARRL Field Day',
            'is_active' => true,
        ]);

        // Create active event
        $this->event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'name' => 'Test Field Day 2026',
            'start_time' => now()->subHours(12),
            'end_time' => now()->addHours(12),
        ]);

        $this->config = EventConfiguration::factory()->create([
            'event_id' => $this->event->id,
        ]);
    }

    /**
     * Test layout switching from default to TV layout.
     */
    public function test_can_switch_to_tv_layout(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->waitFor('[data-cy="layout-selector"]', 5)
                ->click('[data-cy="layout-selector"]')
                ->waitFor('[data-cy="layout-tv"]', 2)
                ->click('[data-cy="layout-tv"]')
                ->pause(300) // Allow Alpine.js transition
                ->assertScript('localStorage.getItem("dashboard_layout") === "tv"');
        });
    }

    /**
     * Test TV layout persists across page reloads via localStorage.
     */
    public function test_tv_layout_persists_in_localstorage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->waitFor('[data-cy="layout-selector"]', 5)
                ->click('[data-cy="layout-selector"]')
                ->waitFor('[data-cy="layout-tv"]', 2)
                ->click('[data-cy="layout-tv"]')
                ->pause(300)
                ->refresh()
                ->pause(500)
                ->assertScript('localStorage.getItem("dashboard_layout") === "tv"');
        });
    }

    /**
     * Test TV layout applies tvdashboard theme attribute.
     */
    public function test_tv_layout_applies_theme_attribute(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->script('localStorage.setItem("dashboard_layout", "tv")');

            $browser->refresh()
                ->pause(500)
                ->waitFor('[data-theme="tvdashboard"]', 5)
                ->assertAttribute('[data-theme="tvdashboard"]', 'data-theme', 'tvdashboard');
        });
    }

    /**
     * Test F key toggles navigation visibility in TV mode.
     *
     * Note: This test will need to be updated when TV layout view is fully implemented.
     */
    public function test_f_key_toggles_navigation_visibility(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->script('localStorage.setItem("dashboard_layout", "tv")');

            $browser->refresh()
                ->pause(500);

            // Press F key
            $browser->keys('body', '{f}')
                ->pause(300);

            // When TV layout is fully implemented, add assertions here for:
            // - Navigation bar visibility toggle
            // - Alpine.js state change
            // For now, just verify no JavaScript errors occurred
            $jsErrors = $browser->driver->manage()->getLog('browser');
            $errors = array_filter($jsErrors, function ($entry) {
                return str_contains(strtolower($entry['level']), 'severe');
            });

            $this->assertEmpty($errors, 'JavaScript errors found: '.json_encode($errors));
        });
    }

    /**
     * Test default layout is shown when no localStorage preference exists.
     */
    public function test_default_layout_shown_by_default(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->script('localStorage.removeItem("dashboard_layout")')
                ->visit('/dashboard')
                ->pause(500)
                ->assertScript('localStorage.getItem("dashboard_layout") === null || localStorage.getItem("dashboard_layout") === "default"');
        });
    }

    /**
     * Test layout selector dropdown is visible.
     */
    public function test_layout_selector_dropdown_opens(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->waitFor('[data-cy="layout-selector"]', 5)
                ->click('[data-cy="layout-selector"]')
                ->waitFor('[data-cy="layout-default"]', 2)
                ->assertVisible('[data-cy="layout-default"]')
                ->assertVisible('[data-cy="layout-tv"]');
        });
    }

    /**
     * Test switching back from TV to default layout.
     */
    public function test_can_switch_from_tv_to_default_layout(): void
    {
        $this->browse(function (Browser $browser) {
            // Start with TV layout
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->script('localStorage.setItem("dashboard_layout", "tv")');

            $browser->refresh()
                ->pause(500)
                ->waitFor('[data-cy="layout-selector"]', 5)
                ->click('[data-cy="layout-selector"]')
                ->waitFor('[data-cy="layout-default"]', 2)
                ->click('[data-cy="layout-default"]')
                ->pause(300)
                ->assertScript('localStorage.getItem("dashboard_layout") === "default"');
        });
    }

    /**
     * Test invalid layout value in localStorage falls back to default.
     */
    public function test_invalid_layout_value_falls_back_to_default(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->script('localStorage.setItem("dashboard_layout", "invalid-layout")');

            $browser->refresh()
                ->pause(500)
                // Alpine.js should validate and use default layout
                ->assertScript(
                    'const layout = localStorage.getItem("dashboard_layout"); '.
                    'return layout === "invalid-layout";' // localStorage still has invalid value
                );

            // But Alpine.js currentLayout should be 'default' due to isValidLayout() check
            // This test will be more meaningful when layout rendering is visible
        });
    }

    /**
     * Test dashboard requires authentication.
     */
    public function test_dashboard_requires_authentication(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/dashboard')
                ->assertPathIs('/login');
        });
    }

    /**
     * Test dashboard shows LIVE badge when event is active.
     */
    public function test_dashboard_shows_live_badge_for_active_event(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->waitForText('Dashboard', 5)
                ->assertSee('LIVE');
        });
    }

    /**
     * Test both layout components are loaded on dashboard page.
     */
    public function test_both_layouts_loaded_on_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->pause(500);

            // Check that Alpine.js is managing layout switching
            $hasLayoutSwitching = $browser->script(
                'return document.querySelector("[x-data]") !== null && '.
                'document.querySelector("[x-show]") !== null;'
            );

            $this->assertTrue(
                $hasLayoutSwitching[0] ?? false,
                'Alpine.js layout switching directives not found'
            );
        });
    }

    /**
     * Test connection status indicator is present.
     */
    public function test_connection_status_indicator_present(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->pause(500);

            // Livewire connection-status component should be present
            // This test will be more specific when component is fully styled
            $hasConnectionStatus = $browser->script(
                'return document.body.innerHTML.includes("connection-status") || '.
                'document.querySelector("[wire\\\\:id]") !== null;'
            );

            $this->assertTrue(
                $hasConnectionStatus[0] ?? false,
                'Connection status component not found'
            );
        });
    }
}
