<?php

namespace Tests\Feature;

use App\Models\Rute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SR14_RouteComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_route_comparison_page_loads_successfully(): void
    {
        $response = $this->get('/rute/bandingkan');

        $response->assertOk();
        $response->assertSee('Perbandingan Alternatif Rute');
        $response->assertSee('tabel-perbandingan');
    }

    public function test_backend_route_comparison_api_evaluates_routes_accurately(): void
    {
        // Rute A: Cepat tapi kurang aman
        $ruteA = Rute::create([
            'start_location' => 'Titik A',
            'destination' => 'Titik B',
            'distance' => 4.20,
            'duration' => 10,
            'security_score' => 65.00,
            'security_level' => 'Waspada',
        ]);

        // Rute B: Lebih jauh tapi paling aman
        $ruteB = Rute::create([
            'start_location' => 'Titik A',
            'destination' => 'Titik B',
            'distance' => 5.80,
            'duration' => 15,
            'security_score' => 88.50,
            'security_level' => 'Aman',
        ]);

        $response = $this->getJson('/api/routes/compare?' . http_build_query([
            'ids' => [$ruteA->route_id, $ruteB->route_id],
        ]));

        $response->assertOk();
        $response->assertJson([
            'recommended_id' => $ruteB->route_id,
        ]);

        $routesData = $response->json('routes');
        $this->assertCount(2, $routesData);

        // Periksa penanda rute tercepat dan teraman
        $ruteAResult = collect($routesData)->firstWhere('id', $ruteA->route_id);
        $ruteBResult = collect($routesData)->firstWhere('id', $ruteB->route_id);

        $this->assertTrue($ruteAResult['is_fastest']);
        $this->assertTrue($ruteAResult['is_shortest']);
        $this->assertFalse($ruteAResult['is_safest']);

        $this->assertTrue($ruteBResult['is_safest']);
        $this->assertFalse($ruteBResult['is_fastest']);
    }

    public function test_backend_comparison_rejects_single_route_or_more_than_three(): void
    {
        $rute = Rute::create([
            'start_location' => 'A',
            'destination' => 'B',
            'distance' => 3.0,
            'duration' => 8,
            'security_score' => 75.0,
            'security_level' => 'Waspada',
        ]);

        // Mengirim hanya 1 ID (harus ditolak karena minimal 2 rute untuk perbandingan)
        $response = $this->getJson('/api/routes/compare?' . http_build_query([
            'ids' => [$rute->route_id],
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('ids');
    }
}
