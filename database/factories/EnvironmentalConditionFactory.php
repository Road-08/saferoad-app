<?php

namespace Database\Factories;

use App\Models\EnvironmentalCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnvironmentalCondition>
 */
class EnvironmentalConditionFactory extends Factory
{
    protected $model = EnvironmentalCondition::class;

    public function definition(): array
    {
        return [
            'location_name' => fake()->streetName(),
            'latitude' => fake()->latitude(-7.3300, -7.3000),
            'longitude' => fake()->longitude(112.7100, 112.7500),
            'activity_level' => fake()->randomElement(['ramai', 'sedang', 'sepi']),
            'lighting_condition' => fake()->randomElement(['terang', 'cukup', 'minim', 'gelap']),
            'observed_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'validation_status' => 'verified',
            'is_simulation' => true,
        ];
    }

    public function crowdedAndBright(): static
    {
        return $this->state(fn () => [
            'activity_level' => 'ramai',
            'lighting_condition' => 'terang',
            'validation_status' => 'verified',
        ]);
    }

    public function quietAndDark(): static
    {
        return $this->state(fn () => [
            'activity_level' => 'sepi',
            'lighting_condition' => 'gelap',
            'validation_status' => 'verified',
        ]);
    }
}
