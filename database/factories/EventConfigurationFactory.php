<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventConfiguration>
 */
class EventConfigurationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => \App\Models\Event::factory(),
            'created_by_user_id' => \App\Models\User::factory(),
            'callsign' => 'W1AW',
            'club_name' => 'Test Radio Club',
            'is_active' => true,
            'section_id' => \App\Models\Section::where('abbreviation', 'CT')->first()?->id ?? 1,
            'operating_class_id' => \App\Models\OperatingClass::first()?->id ?? 1,
            'transmitter_count' => 1,
            'has_gota_station' => false,
            'max_power_watts' => 100,
            'power_multiplier' => '2',
            'uses_commercial_power' => true,
            'uses_generator' => false,
            'uses_battery' => false,
            'uses_solar' => false,
            'uses_wind' => false,
            'uses_water' => false,
            'uses_methane' => false,
            'uses_other_power' => false,
        ];
    }
}
