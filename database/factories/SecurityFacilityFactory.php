<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\SecurityFacility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SecurityFacility>
 */
class SecurityFacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'facility_type' => fake()->randomElement(['cctv', 'penerangan', 'pos_keamanan', 'titik_aman']),
            'facility_name' => fake()->unique()->streetName().' Safety Point',
            'description' => fake()->sentence(),
            'status' => 'aktif',
        ];
    }
}
