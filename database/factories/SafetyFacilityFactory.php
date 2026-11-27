<?php

namespace Database\Factories;

use App\Models\SafetyFacility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SafetyFacility>
 */
class SafetyFacilityFactory extends Factory
{
    protected $model = SafetyFacility::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['street_light', 'cctv', 'security_post']),
            'latitude' => fake()->latitude(-7.3300, -7.3000),
            'longitude' => fake()->longitude(112.7100, 112.7500),
            'address' => fake()->address(),
            'status' => 'active',
            'condition' => 'aktif',
            'verification_status' => 'verified',
            'description' => '[DATA SIMULASI PENGUJIAN] Fasilitas keamanan simulasi.',
            'is_simulation' => true,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'condition' => 'aktif',
            'verification_status' => 'verified',
        ]);
    }

    public function damaged(): static
    {
        return $this->state(fn () => [
            'status' => 'inactive',
            'condition' => 'rusak',
        ]);
    }
}
