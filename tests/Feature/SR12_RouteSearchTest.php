<?php

namespace Tests\Feature;

use Tests\TestCase;

class SR12_RouteSearchTest extends TestCase
{
    public function test_route_search_page_loads_successfully(): void
    {
        $response = $this->get('/rute');

        $response->assertOk();
        $response->assertSee('Temukan Rute Perjalanan Aman');
        $response->assertSee('inputDari');
        $response->assertSee('inputKe');
        $response->assertSee('btnCari');
    }

    public function test_route_scoring_api_endpoint_must_exist(): void
    {
        // Frontend rute.blade.php memanggil POST /api/routes/score
        // Endpoint ini harus terdaftar di backend untuk menghitung skor keamanan rute.
        $response = $this->postJson('/api/routes/score', [
            'routes' => [],
        ]);

        $this->assertNotEquals(
            404,
            $response->status(),
            'Endpoint POST /api/routes/score belum terdaftar di backend (404 Not Found).'
        );
    }
}
