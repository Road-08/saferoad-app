<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'latitude' => fake()->randomFloat(7, -7.325, -7.303),
            'longitude' => fake()->randomFloat(7, 112.715, 112.735),
            'address' => fake()->streetAddress(),
        ];
    }
}
