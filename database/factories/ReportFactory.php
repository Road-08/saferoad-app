<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'location_id' => Location::factory(),
            'category' => fake()->randomElement(['kecelakaan', 'kriminalitas', 'pelecehan']),
            'event_time' => fake()->dateTimeBetween('-30 days'),
            'description' => fake()->sentence(),
            'status' => 'menunggu_validasi',
            'admin_note' => null,
        ];
    }
}
