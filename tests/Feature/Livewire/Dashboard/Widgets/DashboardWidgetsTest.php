<?php

use App\Livewire\Dashboard\Widgets\QsoCount;
use App\Livewire\Dashboard\Widgets\Score;
use App\Livewire\Dashboard\Widgets\TimeRemaining;
use App\Models\Contact;
use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\EventType;
use Livewire\Livewire;

beforeEach(function () {
    $this->eventType = EventType::create([
        'code' => 'FD',
        'name' => 'Field Day',
        'description' => 'ARRL Field Day',
        'is_active' => true,
    ]);

    // Note: Use appNow() instead of now() for event times to work with dev time travel
});

// ============================================================================
// QSO COUNT WIDGET TESTS
// ============================================================================

describe('QsoCount Widget', function () {
    test('component renders successfully', function () {
        Livewire::test(QsoCount::class)
            ->assertOk();
    });

    test('component mounts with tvMode parameter', function () {
        Livewire::test(QsoCount::class, ['tvMode' => true])
            ->assertSet('tvMode', true);

        Livewire::test(QsoCount::class, ['tvMode' => false])
            ->assertSet('tvMode', false);
    });

    test('component finds active event on mount', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(QsoCount::class);

        expect($component->event)->not->toBeNull();
        expect($component->event->id)->toBe($event->id);
    });

    test('component has null event when no active event exists', function () {
        // Create a future event (not active)
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->addDays(30),
            'end_time' => appNow()->addDays(31),
        ]);

        $component = Livewire::test(QsoCount::class);

        expect($component->event)->toBeNull();
    });

    test('qsoCount returns zero when no event exists', function () {
        $component = Livewire::test(QsoCount::class);

        expect($component->qsoCount)->toBe(0);
    });

    test('qsoCount returns zero when event has no configuration', function () {
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(QsoCount::class);

        expect($component->qsoCount)->toBe(0);
    });

    test('qsoCount returns correct count for active event', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $config = EventConfiguration::factory()->create([
            'event_id' => $event->id,
        ]);

        Contact::factory()->count(15)->create([
            'event_configuration_id' => $config->id,
        ]);

        $component = Livewire::test(QsoCount::class);

        expect($component->qsoCount)->toBe(15);
    });

    test('qsoCount excludes duplicate contacts', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $config = EventConfiguration::factory()->create([
            'event_id' => $event->id,
        ]);

        // Create 10 non-duplicate contacts
        Contact::factory()->count(10)->create([
            'event_configuration_id' => $config->id,
            'is_duplicate' => false,
        ]);

        // Create 5 duplicate contacts
        Contact::factory()->count(5)->create([
            'event_configuration_id' => $config->id,
            'is_duplicate' => true,
        ]);

        $component = Livewire::test(QsoCount::class);

        // Should only count non-duplicates
        expect($component->qsoCount)->toBe(10);
    });

    test('qsoRate returns zero when no event exists', function () {
        $component = Livewire::test(QsoCount::class);

        expect($component->qsoRate)->toBe(0.0);
    });

    test('qsoRate returns zero when event just started', function () {
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => now(),
            'end_time' => appNow()->addHours(24),
        ]);

        $component = Livewire::test(QsoCount::class);

        expect($component->qsoRate)->toBe(0.0);
    });

    test('qsoRate calculates correct rate per hour', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(2), // 2 hours elapsed
            'end_time' => appNow()->addHours(22),
        ]);

        $config = EventConfiguration::factory()->create([
            'event_id' => $event->id,
        ]);

        // Create 10 contacts over 2 hours = 5 QSOs/hour
        Contact::factory()->count(10)->create([
            'event_configuration_id' => $config->id,
        ]);

        $component = Livewire::test(QsoCount::class);

        expect($component->qsoRate)->toBe(5.0);
    });

    test('qsoRate rounds to one decimal place', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subMinutes(90), // 1.5 hours
            'end_time' => appNow()->addHours(22),
        ]);

        $config = EventConfiguration::factory()->create([
            'event_id' => $event->id,
        ]);

        // 10 contacts in 1.5 hours = 6.666... per hour
        Contact::factory()->count(10)->create([
            'event_configuration_id' => $config->id,
        ]);

        $component = Livewire::test(QsoCount::class);

        expect($component->qsoRate)->toBe(6.7);
    });

    test('component eager loads eventConfiguration relationship', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        EventConfiguration::factory()->create([
            'event_id' => $event->id,
        ]);

        $component = Livewire::test(QsoCount::class);

        expect($component->event->relationLoaded('eventConfiguration'))->toBeTrue();
    });
});

// ============================================================================
// SCORE WIDGET TESTS
// ============================================================================

describe('Score Widget', function () {
    test('component renders successfully', function () {
        Livewire::test(Score::class)
            ->assertOk();
    });

    test('component mounts with tvMode parameter', function () {
        Livewire::test(Score::class, ['tvMode' => true])
            ->assertSet('tvMode', true);

        Livewire::test(Score::class, ['tvMode' => false])
            ->assertSet('tvMode', false);
    });

    test('component finds active event on mount', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(Score::class);

        expect($component->event)->not->toBeNull();
        expect($component->event->id)->toBe($event->id);
    });

    test('qsoScore returns zero when no event exists', function () {
        $component = Livewire::test(Score::class);

        expect($component->qsoScore)->toBe(0);
    });

    test('qsoScore returns zero when event has no configuration', function () {
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(Score::class);

        expect($component->qsoScore)->toBe(0);
    });

    test('bonusScore returns zero when no event exists', function () {
        $component = Livewire::test(Score::class);

        expect($component->bonusScore)->toBe(0);
    });

    test('bonusScore returns zero when event has no configuration', function () {
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(Score::class);

        expect($component->bonusScore)->toBe(0);
    });

    test('finalScore returns zero when no event exists', function () {
        $component = Livewire::test(Score::class);

        expect($component->finalScore)->toBe(0);
    });

    test('finalScore returns zero when event has no configuration', function () {
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(Score::class);

        expect($component->finalScore)->toBe(0);
    });

    test('powerMultiplier returns 1 when no event exists', function () {
        $component = Livewire::test(Score::class);

        expect($component->powerMultiplier)->toBe(1);
    });

    test('powerMultiplier returns 1 when event has no configuration', function () {
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(Score::class);

        expect($component->powerMultiplier)->toBe(1);
    });

    test('powerMultiplier delegates to EventConfiguration', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        EventConfiguration::factory()->create([
            'event_id' => $event->id,
            'max_power_watts' => 5,
            'uses_battery' => true,
            'uses_commercial_power' => false,
            'uses_generator' => false,
        ]);

        $component = Livewire::test(Score::class);

        // 5W QRP with natural power = 5x multiplier
        expect($component->powerMultiplier)->toBe(5);
    });

    test('component eager loads eventConfiguration relationship', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        EventConfiguration::factory()->create([
            'event_id' => $event->id,
        ]);

        $component = Livewire::test(Score::class);

        expect($component->event->relationLoaded('eventConfiguration'))->toBeTrue();
    });
});

// ============================================================================
// TIME REMAINING WIDGET TESTS
// ============================================================================

describe('TimeRemaining Widget', function () {
    test('component renders successfully', function () {
        Livewire::test(TimeRemaining::class)
            ->assertOk();
    });

    test('component mounts with tvMode parameter', function () {
        Livewire::test(TimeRemaining::class, ['tvMode' => true])
            ->assertSet('tvMode', true);

        Livewire::test(TimeRemaining::class, ['tvMode' => false])
            ->assertSet('tvMode', false);
    });

    test('component finds active event on mount', function () {
        $event = Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        expect($component->event)->not->toBeNull();
        expect($component->event->id)->toBe($event->id);
    });

    test('timeRemaining returns zeros when no event exists', function () {
        $component = Livewire::test(TimeRemaining::class);

        $time = $component->timeRemaining;
        expect($time['total_seconds'])->toBe(0);
        expect($time['hours'])->toBe(0);
        expect($time['minutes'])->toBe(0);
        expect($time['seconds'])->toBe(0);
        expect($time['formatted'])->toBe('00:00:00');
        expect($time['percentage'])->toBe(0);
    });

    test('timeRemaining returns zeros when event has no end_time', function () {
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => null,
        ]);

        $component = Livewire::test(TimeRemaining::class);

        $time = $component->timeRemaining;
        expect($time['total_seconds'])->toBe(0);
        expect($time['formatted'])->toBe('00:00:00');
    });

    test('timeRemaining calculates correct hours and minutes for active event', function () {
        // Event that is currently active, ending in approximately 5 hours, 30 minutes
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(1), // Started 1 hour ago
            'end_time' => appNow()->addHours(5)->addMinutes(30),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        $time = $component->timeRemaining;
        expect($time['hours'])->toBeIn([5, 5.0]); // floor() returns float
        expect($time['minutes'])->toBeIn([29, 29.0, 30, 30.0]); // floor() returns float, allow variance
        expect($time['total_seconds'])->toBeGreaterThan(19700); // ~5.5 hours
    });

    test('timeRemaining formats time correctly for active event', function () {
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(1), // Started 1 hour ago
            'end_time' => appNow()->addHours(3)->addMinutes(45),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        $time = $component->timeRemaining;
        // Allow for test execution time variance (could be 03:44:XX or 03:45:XX)
        expect($time['formatted'])->toMatch('/^03:(44|45):[0-5][0-9]$/');
    });

    test('timeRemaining returns zeros when no active event exists', function () {
        // Create a past event (not active)
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(26),
            'end_time' => appNow()->subHours(2),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        $time = $component->timeRemaining;
        expect($time['formatted'])->toBe('00:00:00');
        expect($time['total_seconds'])->toBe(0);
        expect($time['percentage'])->toBe(0);
    });

    test('timeRemaining calculates percentage correctly for active event', function () {
        // 24-hour event, 6 hours elapsed = 25% complete
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(6),
            'end_time' => appNow()->addHours(18),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        $time = $component->timeRemaining;
        expect($time['percentage'])->toBe(25.0);
    });

    test('timeRemaining percentage rounds to one decimal place for active event', function () {
        // 24-hour event, 8 hours elapsed = 33.333...%
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(8),
            'end_time' => appNow()->addHours(16),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        $time = $component->timeRemaining;
        expect($time['percentage'])->toBe(33.3);
    });

    test('eventStatus returns No Active Event when no event exists', function () {
        $component = Livewire::test(TimeRemaining::class);

        expect($component->eventStatus)->toBe('No Active Event');
    });

    test('eventStatus returns No Active Event when event is in the future', function () {
        // Future event won't be found by Event::active() scope
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->addHours(2),
            'end_time' => appNow()->addHours(26),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        expect($component->eventStatus)->toBe('No Active Event');
    });

    test('eventStatus returns In Progress when event is active', function () {
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        expect($component->eventStatus)->toBe('In Progress');
    });

    test('eventStatus returns No Active Event when event is over', function () {
        // Past event won't be found by Event::active() scope
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(26),
            'end_time' => appNow()->subHours(2),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        expect($component->eventStatus)->toBe('No Active Event');
    });

    test('timeRemaining uses appNow for time calculations on active event', function () {
        // This test verifies that appNow() is used (important for dev time travel)
        Event::factory()->create([
            'event_type_id' => $this->eventType->id,
            'start_time' => appNow()->subHours(12),
            'end_time' => appNow()->addHours(12),
        ]);

        $component = Livewire::test(TimeRemaining::class);

        // If appNow() is used correctly, we should get valid time remaining
        $time = $component->timeRemaining;
        expect($time['total_seconds'])->toBeGreaterThan(0);
    });
});
