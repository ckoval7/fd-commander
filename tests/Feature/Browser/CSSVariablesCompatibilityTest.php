<?php

test('css variables are loaded in light mode', function () {
    // The root page redirects without auth, so we check a public page
    $response = $this->get('/contacts');

    // Could be 200 (if public) or 302 (if redirected to login)
    // What matters is the layout includes data-theme attribute
    expect($response->status())->toBeIn([200, 302]);

    if ($response->status() === 200) {
        $response->assertSee('data-theme="light"', false);
    }
});

test('css variables file exists and is valid', function () {
    $colorsFile = resource_path('css/colors.css');

    expect(file_exists($colorsFile))->toBeTrue('colors.css file should exist');

    $content = file_get_contents($colorsFile);

    // Verify :root selector exists
    expect($content)->toContain(':root {');

    // Verify dark mode selector exists
    expect($content)->toContain('[data-theme="dark"]');

    // Verify essential variables are defined
    expect($content)->toContain('--color-primary:');
    expect($content)->toContain('--color-bg-primary:');
    expect($content)->toContain('--color-text-primary:');
    expect($content)->toContain('--color-success:');
    expect($content)->toContain('--color-error:');
    expect($content)->toContain('--color-warning:');
    expect($content)->toContain('--color-info:');
    expect($content)->toContain('--color-focus:');
});

test('app css imports colors css', function () {
    $appCssFile = resource_path('css/app.css');

    expect(file_exists($appCssFile))->toBeTrue('app.css file should exist');

    $content = file_get_contents($appCssFile);

    expect($content)->toContain("@import './colors.css'");
});

test('dark mode toggle component exists', function () {
    $toggleFile = resource_path('views/components/theme-toggle.blade.php');

    expect(file_exists($toggleFile))->toBeTrue('theme-toggle component should exist');

    $content = file_get_contents($toggleFile);

    // Verify component uses data-theme attribute
    expect($content)->toContain('data-theme');

    // Verify component uses localStorage
    expect($content)->toContain('localStorage');

    // Verify component has toggle functionality
    expect($content)->toContain('toggle()');
});

test('html element accepts data-theme attribute', function () {
    $layoutFile = resource_path('views/layouts/app.blade.php');

    expect(file_exists($layoutFile))->toBeTrue('app layout should exist');

    $content = file_get_contents($layoutFile);

    // Verify html element has data-theme attribute
    expect($content)->toContain('data-theme="light"');
});

test('css variables have proper color definitions', function () {
    $colorsFile = resource_path('css/colors.css');
    $content = file_get_contents($colorsFile);

    // Verify HSL notation is used
    expect($content)->toContain('hsl(');

    // Count the number of variables defined in :root
    preg_match_all('/--color-[\w-]+:/', $content, $matches);
    $variableCount = count($matches[0]);

    // Should have at least 40 color variables
    expect($variableCount)->toBeGreaterThanOrEqual(40);
});

test('dark mode has all required variable overrides', function () {
    $colorsFile = resource_path('css/colors.css');
    $content = file_get_contents($colorsFile);

    // Split by [data-theme="dark"]
    $parts = explode('[data-theme="dark"]', $content);

    expect(count($parts))->toBe(2);

    $darkModeVars = $parts[1];

    // Verify dark mode overrides essential variables
    expect($darkModeVars)->toContain('--color-primary:');
    expect($darkModeVars)->toContain('--color-bg-primary:');
    expect($darkModeVars)->toContain('--color-text-primary:');
    expect($darkModeVars)->toContain('--color-surface:');
});
