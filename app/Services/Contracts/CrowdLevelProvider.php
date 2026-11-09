<?php

namespace App\Services\Contracts;

interface CrowdLevelProvider
{
    /**
     * @param  array<int, array{0: float, 1: float}>  $routeCoordinates
     * @param  array<int, array{0: float, 1: float}>  $sampleCoordinates
     */
    public function scoreForRoute(array $routeCoordinates, array $sampleCoordinates, int $minimumSamples): ?float;
}
