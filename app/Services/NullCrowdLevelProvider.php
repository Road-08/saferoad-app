<?php

namespace App\Services;

use App\Services\Contracts\CrowdLevelProvider;

class NullCrowdLevelProvider implements CrowdLevelProvider
{
    /**
     * @param  array<int, array{0: float, 1: float}>  $routeCoordinates
     * @param  array<int, array{0: float, 1: float}>  $sampleCoordinates
     */
    public function scoreForRoute(array $routeCoordinates, array $sampleCoordinates, int $minimumSamples): ?float
    {
        return null;
    }
}
