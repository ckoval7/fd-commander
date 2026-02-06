<?php

use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\EventType;

beforeEach(function () {
    // Ensure required data is seeded
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\EventTypeSeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\SectionSeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\OperatingClassSeeder']);
});

test('event has event type relationship', function () {
    $event = Event::factory()->create();

    expect($event->eventType)->toBeInstanceOf(EventType::class);
});

test('event has event configuration relationship', function () {
    $event = Event::factory()->create();
    $config = EventConfiguration::factory()->create(['event_id' => $event->id]);

    expect($event->eventConfiguration)->toBeInstanceOf(EventConfiguration::class);
    expect($event->eventConfiguration->id)->toBe($config->id);
});

test('event status is active when within date range', function () {
    $event = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);

    expect($event->fresh()->status)->toBe('active');
});

test('event status is upcoming when start time is in future', function () {
    $event = Event::factory()->create([
        'start_time' => now()->addDays(7),
        'end_time' => now()->addDays(8),
    ]);

    expect($event->status)->toBe('upcoming');
});

test('event status is active when within time range', function () {
    $event = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);

    expect($event->status)->toBe('active');
});

test('event status is completed when end time is past', function () {
    $event = Event::factory()->create([
        'start_time' => now()->subDays(8),
        'end_time' => now()->subDays(7),
    ]);

    expect($event->status)->toBe('completed');
});

test('event scopes filter correctly', function () {
    $active = Event::factory()->create([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ]);
    $upcoming = Event::factory()->create(['start_time' => now()->addDays(7), 'end_time' => now()->addDays(8)]);
    $completed = Event::factory()->create(['start_time' => now()->subDays(8), 'end_time' => now()->subDays(7)]);

    expect(Event::active()->count())->toBe(1);
    expect(Event::upcoming()->count())->toBe(1);
    expect(Event::completed()->count())->toBe(1);
});
