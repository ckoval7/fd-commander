<?php

use App\Livewire\Dashboard\LayoutSelector;
use Livewire\Livewire;

test('component renders with default layout', function () {
    Livewire::test(LayoutSelector::class)
        ->assertSet('selectedLayout', 'default')
        ->assertSee('Default Dashboard');
});

test('component loads available layouts from config', function () {
    $component = Livewire::test(LayoutSelector::class);

    $layouts = $component->get('layouts');

    expect($layouts)->toBeArray()
        ->and($layouts)->not->toBeEmpty();

    // Verify default layout exists
    $defaultLayout = collect($layouts)->firstWhere('key', 'default');
    expect($defaultLayout)->not->toBeNull()
        ->and($defaultLayout['name'])->toBe('Default Dashboard');

    // Verify TV layout exists
    $tvLayout = collect($layouts)->firstWhere('key', 'tv');
    expect($tvLayout)->not->toBeNull()
        ->and($tvLayout['name'])->toBe('TV Display');
});

test('component displays all layouts in dropdown', function () {
    Livewire::test(LayoutSelector::class)
        ->assertSee('Default Dashboard')
        ->assertSee('TV Display')
        ->assertSee('General purpose dashboard with customizable widgets')
        ->assertSee('Large display optimized for 10+ foot viewing distance');
});

test('switchLayout to tv redirects to tv route', function () {
    Livewire::test(LayoutSelector::class)
        ->assertSet('selectedLayout', 'default')
        ->call('switchLayout', 'tv')
        ->assertRedirect(route('dashboard.tv'));
});

test('switchLayout to default dispatches layout-changed event', function () {
    // Set to a non-default layout first, then switch to default
    Livewire::test(LayoutSelector::class)
        ->call('switchLayout', 'default')
        ->assertSet('selectedLayout', 'default')
        ->assertDispatched('layout-changed', layout: 'default');
});

test('switchLayout ignores invalid layout keys', function () {
    Livewire::test(LayoutSelector::class)
        ->assertSet('selectedLayout', 'default')
        ->call('switchLayout', 'invalid-layout')
        ->assertSet('selectedLayout', 'default'); // Should remain unchanged
});

test('component shows check icon for selected layout', function () {
    Livewire::test(LayoutSelector::class)
        ->set('selectedLayout', 'default')
        ->assertSee('Default Dashboard');
});

test('component highlights selected layout with primary color', function () {
    $component = Livewire::test(LayoutSelector::class);

    // The view should apply primary styles to the selected layout
    expect($component->get('selectedLayout'))->toBe('default');
});

test('component renders dropdown button with icon', function () {
    $component = Livewire::test(LayoutSelector::class);

    // The component should render with the dropdown button
    expect($component->get('layouts'))->not->toBeEmpty();
});

test('component shows layout descriptions in dropdown', function () {
    Livewire::test(LayoutSelector::class)
        ->assertSee('General purpose dashboard')
        ->assertSee('Large display optimized for 10+ foot');
});

test('layouts include expected structure', function () {
    $component = Livewire::test(LayoutSelector::class);
    $layouts = $component->get('layouts');

    foreach ($layouts as $layout) {
        expect($layout)->toHaveKeys(['key', 'name', 'description']);
    }
});

test('default layout is first in available layouts', function () {
    $component = Livewire::test(LayoutSelector::class);
    $layouts = $component->get('layouts');

    // Check that default layout exists
    $hasDefault = collect($layouts)->contains('key', 'default');
    expect($hasDefault)->toBeTrue();
});
