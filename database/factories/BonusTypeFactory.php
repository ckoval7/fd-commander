<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BonusType>
 */
class BonusTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_type_id' => 1, // Field Day
            'code' => fake()->unique()->slug(2),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'base_points' => fake()->randomElement([50, 100, 200, 500]),
            'is_per_transmitter' => fake()->boolean(),
            'is_per_occurrence' => fake()->boolean(),
            'max_points' => fake()->optional()->randomElement([500, 1000, 2000]),
            'max_occurrences' => fake()->optional()->numberBetween(1, 10),
            'requires_proof' => fake()->boolean(),
            'eligible_classes' => ['A', 'B', 'D', 'E', 'F'],
            'is_active' => true,
        ];
    }
}
