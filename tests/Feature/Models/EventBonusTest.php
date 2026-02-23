<?php

use App\Models\BonusType;
use App\Models\EventBonus;
use App\Models\EventType;

it('has a bonusType relationship', function () {
    $eventType = EventType::create([
        'code' => 'FD',
        'name' => 'Field Day',
        'description' => 'ARRL Field Day',
        'is_active' => true,
    ]);

    $bonusType = BonusType::where('event_type_id', $eventType->id)->first()
        ?? BonusType::factory()->create(['event_type_id' => $eventType->id]);

    $bonus = EventBonus::factory()->create(['bonus_type_id' => $bonusType->id]);

    expect($bonus->bonusType)->not->toBeNull();
    expect($bonus->bonusType->id)->toBe($bonusType->id);
});
