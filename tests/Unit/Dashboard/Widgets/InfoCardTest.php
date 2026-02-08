<?php

use App\Livewire\Dashboard\Widgets\InfoCard;
use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\OperatingClass;
use App\Models\Section;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

beforeEach(function () {
    // Clear cache before each test
    Cache::flush();
});

test('info card component can be instantiated', function () {
    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $component->assertStatus(200);
});

test('info card returns empty data when no active event', function () {
    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data = $component->viewData('data');

    expect($data)
        ->toBeArray()
        ->toHaveKeys(['event_name', 'location', 'operating_class', 'call_sign'])
        ->and($data['event_name'])->toBe('N/A')
        ->and($data['location'])->toBe('N/A')
        ->and($data['operating_class'])->toBe('N/A')
        ->and($data['call_sign'])->toBe('N/A');
});

test('info card returns empty data when event has no configuration', function () {
    // Create event without configuration
    $event = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);

    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data = $component->viewData('data');

    expect($data)
        ->toBeArray()
        ->and($data['event_name'])->toBe('N/A')
        ->and($data['location'])->toBe('N/A')
        ->and($data['operating_class'])->toBe('N/A')
        ->and($data['call_sign'])->toBe('N/A');
});

test('info card displays event name correctly', function () {
    $event = Event::factory()->create([
        'name' => 'Field Day 2025',
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    EventConfiguration::factory()->create(['event_id' => $event->id]);

    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data = $component->viewData('data');

    expect($data['event_name'])->toBe('Field Day 2025');
});

test('info card displays call sign correctly', function () {
    $event = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    EventConfiguration::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'W1AW',
    ]);

    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data = $component->viewData('data');

    expect($data['call_sign'])->toBe('W1AW');
});

test('info card displays section location correctly', function () {
    $section = Section::factory()->create([
        'name' => 'Connecticut',
        'code' => 'CT',
    ]);

    $event = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    EventConfiguration::factory()->create([
        'event_id' => $event->id,
        'section_id' => $section->id,
    ]);

    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data = $component->viewData('data');

    expect($data['location'])->toBe('Connecticut');
});

test('info card displays operating class correctly', function () {
    $eventType = \App\Models\EventType::firstOrCreate(
        ['code' => 'FD'],
        ['name' => 'Field Day', 'description' => 'ARRL Field Day']
    );

    $operatingClass = OperatingClass::create([
        'event_type_id' => $eventType->id,
        'name' => 'Class A',
        'code' => 'A',
        'description' => 'Test Class A',
    ]);

    $event = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    EventConfiguration::factory()->create([
        'event_id' => $event->id,
        'operating_class_id' => $operatingClass->id,
    ]);

    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data = $component->viewData('data');

    expect($data['operating_class'])->toBe('Class A');
});

test('info card displays all data correctly when fully populated', function () {
    $section = Section::factory()->create([
        'name' => 'Eastern Massachusetts',
        'code' => 'EM',
    ]);

    $eventType = \App\Models\EventType::firstOrCreate(
        ['code' => 'FD'],
        ['name' => 'Field Day', 'description' => 'ARRL Field Day']
    );

    $operatingClass = OperatingClass::create([
        'event_type_id' => $eventType->id,
        'name' => 'Class D',
        'code' => 'D',
        'description' => 'Test Class D',
    ]);

    $event = Event::factory()->create([
        'name' => 'Field Day 2025',
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    EventConfiguration::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'K1MZ',
        'section_id' => $section->id,
        'operating_class_id' => $operatingClass->id,
    ]);

    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data = $component->viewData('data');

    expect($data)
        ->and($data['event_name'])->toBe('Field Day 2025')
        ->and($data['call_sign'])->toBe('K1MZ')
        ->and($data['location'])->toBe('Eastern Massachusetts')
        ->and($data['operating_class'])->toBe('Class D');
});

test('info card handles section relationship gracefully', function () {
    $event = Event::factory()->create([
        'name' => 'Field Day 2025',
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);

    EventConfiguration::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'W1XYZ',
    ]);

    // Component should still render without error
    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data = $component->viewData('data');

    // Should have location from factory's default section
    expect($data['location'])->not->toBe('N/A');
});

test('info card handles operating class relationship gracefully', function () {
    $event = Event::factory()->create([
        'name' => 'Field Day 2025',
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);

    EventConfiguration::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'W1XYZ',
    ]);

    // Component should still render without error
    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data = $component->viewData('data');

    // Should have operating_class from factory's default class
    expect($data['operating_class'])->not->toBe('N/A');
});

test('info card caches results for 60 seconds', function () {
    $event = Event::factory()->create([
        'name' => 'Field Day 2025',
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    EventConfiguration::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'W1AW',
    ]);

    // First call - should cache
    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data1 = $component->viewData('data');
    expect($data1['call_sign'])->toBe('W1AW');

    // Update the call sign in the database
    $event->eventConfiguration->update(['callsign' => 'W2XYZ']);

    // Second call - should return cached value (still W1AW)
    $component2 = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $data2 = $component2->viewData('data');
    expect($data2['call_sign'])->toBe('W1AW'); // Still cached value
});

test('info card uses IsWidget trait', function () {
    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'tv',
        'widgetId' => 'test-info-card-123',
    ]);

    expect($component->get('size'))->toBe('tv')
        ->and($component->get('widgetId'))->toBe('test-info-card-123')
        ->and($component->get('config'))->toBe([]);
});

test('info card generates correct cache key', function () {
    $event = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    EventConfiguration::factory()->create(['event_id' => $event->id]);

    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $cacheKey = $component->instance()->cacheKey();

    expect($cacheKey)->toBeString()
        ->toContain('dashboard:widget:InfoCard')
        ->toContain((string) $event->id);
});

test('info card returns empty listeners array', function () {
    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $listeners = $component->instance()->getWidgetListeners();

    expect($listeners)->toBeArray()->toBeEmpty();
});

test('info card renders in tv size variant', function () {
    $section = Section::factory()->create(['name' => 'Connecticut']);

    $eventType = \App\Models\EventType::firstOrCreate(
        ['code' => 'FD'],
        ['name' => 'Field Day', 'description' => 'ARRL Field Day']
    );

    $operatingClass = OperatingClass::create([
        'event_type_id' => $eventType->id,
        'name' => 'Class A',
        'code' => 'A',
        'description' => 'Test Class A',
    ]);

    $event = Event::factory()->create([
        'name' => 'Field Day 2025',
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    EventConfiguration::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'W1AW',
        'section_id' => $section->id,
        'operating_class_id' => $operatingClass->id,
    ]);

    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'tv',
    ]);

    $component->assertViewHas('size', 'tv');
    $data = $component->viewData('data');
    expect($data['event_name'])->toBe('Field Day 2025');
});

test('info card renders in normal size variant', function () {
    $section = Section::factory()->create(['name' => 'Connecticut']);

    $eventType = \App\Models\EventType::firstOrCreate(
        ['code' => 'FD'],
        ['name' => 'Field Day', 'description' => 'ARRL Field Day']
    );

    $operatingClass = OperatingClass::create([
        'event_type_id' => $eventType->id,
        'name' => 'Class A',
        'code' => 'A',
        'description' => 'Test Class A',
    ]);

    $event = Event::factory()->create([
        'name' => 'Field Day 2025',
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    EventConfiguration::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'W1AW',
        'section_id' => $section->id,
        'operating_class_id' => $operatingClass->id,
    ]);

    $component = Livewire::test(InfoCard::class, [
        'config' => [],
        'size' => 'normal',
    ]);

    $component->assertViewHas('size', 'normal');
    $data = $component->viewData('data');
    expect($data['call_sign'])->toBe('W1AW');
});
