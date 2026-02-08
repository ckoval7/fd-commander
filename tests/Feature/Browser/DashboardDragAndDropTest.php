<?php

use App\Models\Dashboard;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

describe('dashboard editor', function () {
    it('can enter edit mode', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Dashboard',
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
                [
                    'id' => 'widget-2',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_score'],
                    'order' => 2,
                    'visible' => true,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}");

        $page->assertSee('Test Dashboard')
            ->assertSee('Edit Mode')
            ->click('Edit Mode')
            ->assertSee('Exit Edit Mode')
            ->assertSee('Save Changes')
            ->assertNoJavaScriptErrors();
    });

    it('can toggle widget visibility in edit mode', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}");

        $page->click('Edit Mode')
            ->waitForText('Exit Edit Mode')
            ->assertNoJavaScriptErrors();

        // Widget should have visibility toggle button
        $page->assertSeeHtml('[wire:click="toggleVisibility(0)"]')
            ->assertNoJavaScriptErrors();
    });

    it('can open widget configurator', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}");

        $page->click('Edit Mode')
            ->waitForText('Exit Edit Mode')
            ->assertNoJavaScriptErrors();

        // Widget should have configure button (gear icon)
        $page->assertSeeHtml('[wire:click="openConfigurator(0)"]')
            ->assertNoJavaScriptErrors();
    });

    it('displays drag handles in edit mode', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
                [
                    'id' => 'widget-2',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_score'],
                    'order' => 2,
                    'visible' => true,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}");

        $page->click('Edit Mode')
            ->waitForText('Exit Edit Mode')
            ->assertNoJavaScriptErrors();

        // Drag handles should be visible in edit mode
        $page->assertSeeHtml('data-sortable-handle')
            ->assertNoJavaScriptErrors();
    });

    it('can exit edit mode without saving', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}");

        $page->click('Edit Mode')
            ->waitForText('Exit Edit Mode')
            ->click('Exit Edit Mode')
            ->assertSee('Edit Mode')
            ->assertDontSee('Save Changes')
            ->assertNoJavaScriptErrors();
    });

    it('shows save changes button in edit mode', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}");

        $page->click('Edit Mode')
            ->waitForText('Exit Edit Mode')
            ->assertSee('Save Changes')
            ->assertSeeHtml('[wire:click="saveDashboard"]')
            ->assertNoJavaScriptErrors();
    });
});

describe('widget management', function () {
    it('displays widgets from config', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
                [
                    'id' => 'widget-2',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_score'],
                    'order' => 2,
                    'visible' => true,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}");

        $page->assertNoJavaScriptErrors()
            ->assertSeeHtml('data-widget-id="widget-1"')
            ->assertSeeHtml('data-widget-id="widget-2"');
    });

    it('hides widgets when visibility is false', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
                [
                    'id' => 'widget-2',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_score'],
                    'order' => 2,
                    'visible' => false,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}");

        // In view mode, hidden widgets should not be visible
        $page->assertSeeHtml('data-widget-id="widget-1"')
            ->assertDontSeeHtml('data-widget-id="widget-2"')
            ->assertNoJavaScriptErrors();

        // In edit mode, hidden widgets should be visible but with opacity-50
        $page->click('Edit Mode')
            ->waitForText('Exit Edit Mode')
            ->assertSeeHtml('data-widget-id="widget-2"')
            ->assertSeeHtml('opacity-50')
            ->assertNoJavaScriptErrors();
    });
});

describe('responsive layout', function () {
    it('displays grid layout on desktop', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}")
            ->on()->desktop();

        $page->assertNoJavaScriptErrors()
            ->assertSeeHtml('lg:grid-cols-3');
    });

    it('displays single column layout on mobile', function () {
        $dashboard = Dashboard::factory()->create([
            'user_id' => $this->user->id,
            'config' => [
                [
                    'id' => 'widget-1',
                    'type' => 'stat_card',
                    'config' => ['metric' => 'total_contacts'],
                    'order' => 1,
                    'visible' => true,
                ],
            ],
        ]);

        $page = visit("/dashboard/{$dashboard->id}")
            ->on()->mobile();

        $page->assertNoJavaScriptErrors()
            ->assertSeeHtml('grid-cols-1');
    });
});
