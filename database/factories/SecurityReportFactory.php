<?php

namespace Database\Factories;

use App\Models\SecurityReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SecurityReport>
 */
class SecurityReportFactory extends Factory
{
    protected $model = SecurityReport::class;

    public function definition(): array
    {
        return [
            'report_code' => 'SR-' . strtoupper(Str::random(6)),
            'user_id' => User::factory(),
            'category' => fake()->randomElement(['Pencurian', 'Perampokan', 'Begal', 'Penipuan', 'Tindak Kriminal Lainnya']),
            'description' => fake()->sentence(),
            'severity' => fake()->randomElement(['rendah', 'sedang', 'tinggi']),
            'incident_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-7.3300, -7.3000),
            'longitude' => fake()->longitude(112.7100, 112.7500),
            'status' => 'map_updated',
            'validation_status' => 'verified',
            'is_simulation' => true,
        ];
    }

    public function generalCrime(string $type = 'Pencurian'): static
    {
        return $this->state(fn () => [
            'category' => $type,
            'description' => "[DATA SIMULASI PENGUJIAN] Insiden tindak kriminal umum: {$type}.",
        ]);
    }

    public function womenRisk(string $type = 'Catcalling'): static
    {
        return $this->state(fn () => [
            'category' => $type,
            'description' => "[DATA SIMULASI PENGUJIAN] Laporan khusus perlindungan perempuan: {$type}.",
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'submitted',
            'validation_status' => 'pending',
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'status' => 'map_updated',
            'validation_status' => 'verified',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'validation_status' => 'rejected',
        ]);
    }
}
