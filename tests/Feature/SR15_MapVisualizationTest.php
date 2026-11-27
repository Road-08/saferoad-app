<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SR15_MapVisualizationTest extends TestCase
{
    public function test_route_map_renders_leaflet_container(): void
    {
        $response = $this->get('/rute');

        $response->assertOk();
        $response->assertSee('leaflet.css');
        $response->assertSee('id="map"', false);
    }

    public function test_security_facility_map_view_should_be_registered_in_routes(): void
    {
        // File resources/views/Keamanan.blade.php berisi visualisasi titik CCTV,
        // penerangan, dan pos aman. View ini harus memiliki route terdaftar.
        $this->assertTrue(
            Route::has('keamanan') || Route::has('peta.keamanan'),
            'Rute untuk view visualisasi keamanan (Keamanan.blade.php) belum didaftarkan di routes/web.php.'
        );
    }
}
