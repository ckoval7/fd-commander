<?php

namespace Database\Factories;

use App\Models\GuestbookEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GuestbookEntry>
 */
class GuestbookEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_configuration_id' => \App\Models\EventConfiguration::factory(),
            'user_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'callsign' => strtoupper(fake()->bothify('??#???')),
            'email' => fake()->optional(0.7)->email(),
            'comments' => fake()->optional(0.6)->sentence(),
            'presence_type' => fake()->randomElement(GuestbookEntry::PRESENCE_TYPES),
            'visitor_category' => fake()->randomElement(GuestbookEntry::VISITOR_CATEGORIES),
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
            'ip_address' => fake()->ipv4(),
        ];
    }

    /**
     * Indicate the entry has been verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_by' => \App\Models\User::factory(),
            'verified_at' => now(),
        ]);
    }

    /**
     * Indicate the entry is in-person.
     */
    public function inPerson(): static
    {
        return $this->state(fn (array $attributes) => [
            'presence_type' => GuestbookEntry::PRESENCE_TYPE_IN_PERSON,
        ]);
    }

    /**
     * Indicate the entry is online.
     */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'presence_type' => GuestbookEntry::PRESENCE_TYPE_ONLINE,
        ]);
    }

    /**
     * Indicate the visitor is an elected official.
     */
    public function electedOfficial(): static
    {
        return $this->state(fn (array $attributes) => [
            'visitor_category' => GuestbookEntry::VISITOR_CATEGORY_ELECTED_OFFICIAL,
        ]);
    }

    /**
     * Indicate the visitor is an ARRL official.
     */
    public function arrlOfficial(): static
    {
        return $this->state(fn (array $attributes) => [
            'visitor_category' => GuestbookEntry::VISITOR_CATEGORY_ARRL_OFFICIAL,
        ]);
    }

    /**
     * Indicate the visitor is from an agency.
     */
    public function agency(): static
    {
        return $this->state(fn (array $attributes) => [
            'visitor_category' => GuestbookEntry::VISITOR_CATEGORY_AGENCY,
        ]);
    }

    /**
     * Indicate the visitor is from the media.
     */
    public function media(): static
    {
        return $this->state(fn (array $attributes) => [
            'visitor_category' => GuestbookEntry::VISITOR_CATEGORY_MEDIA,
        ]);
    }

    /**
     * Associate the entry with a user.
     */
    public function withUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => \App\Models\User::factory(),
        ]);
    }
}
