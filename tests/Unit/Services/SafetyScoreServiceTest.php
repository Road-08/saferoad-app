<?php

namespace Tests\Unit\Services;

use App\Services\SafetyParameters;
use App\Services\SafetyScoreService;
use App\Services\Geometry\Haversine;
use App\Services\Geometry\RouteSampler;
use PHPUnit\Framework\TestCase;

class SafetyScoreServiceTest extends TestCase
{
    public function test_route_with_too_few_sample_points_returns_insufficient_data_without_database_queries(): void
    {
        $parameters = $this->createMock(SafetyParameters::class);
        $parameters->method('get')->willReturnMap([
            ['sample_spacing_m', 25, 25],
            ['max_samples', 400, 400],
        ]);

        $routeSampler = $this->createMock(RouteSampler::class);
        $routeSampler->expects($this->once())
            ->method('sample')
            ->willReturn([
                'coordinates' => [],
                'length_m' => 0.0,
                'samples' => [],
                'spacing_m' => 25.0,
            ]);

        $service = new SafetyScoreService($parameters, $routeSampler, $this->createMock(Haversine::class));
        $result = $service->score([
            ['id' => 'short-route', 'coordinates' => []],
        ]);

        $this->assertSame('short-route', $result[0]['id']);
        $this->assertNull($result[0]['score']);
        $this->assertSame('data_belum_cukup', $result[0]['level']);
        $this->assertSame('Data belum cukup', $result[0]['level_label']);
        $this->assertSame('Data keamanan di sekitar rute ini belum cukup.', $result[0]['note']);
        $this->assertCount(5, $result[0]['factors']);
        $this->assertContainsOnly('array', $result[0]['factors']);
    }
}
