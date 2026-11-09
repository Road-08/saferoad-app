<?php

namespace App\Services\Geometry;

use InvalidArgumentException;

class RouteSampler
{
    public function __construct(private Haversine $haversine) {}

    /**
     * @param  array<int, array{0: float|int, 1: float|int}>  $coordinates
     * @return array{
     *     coordinates: array<int, array{0: float, 1: float}>,
     *     length_m: float,
     *     samples: array<int, array{0: float, 1: float}>,
     *     spacing_m: float
     * }
     */
    public function sample(array $coordinates, float $spacingMeters, int $maximumSamples): array
    {
        if ($spacingMeters <= 0 || $maximumSamples < 2) {
            throw new InvalidArgumentException('Jarak sampling dan jumlah sampel maksimum tidak valid.');
        }

        $normalizedCoordinates = [];

        foreach ($coordinates as $coordinate) {
            if (! is_array($coordinate) || count($coordinate) < 2
                || ! is_numeric($coordinate[0] ?? null) || ! is_numeric($coordinate[1] ?? null)) {
                throw new InvalidArgumentException('Setiap titik rute harus memiliki longitude dan latitude numerik.');
            }

            $normalizedCoordinate = [(float) $coordinate[0], (float) $coordinate[1]];
            $this->haversine->distanceMeters($normalizedCoordinate, $normalizedCoordinate);

            if ($normalizedCoordinates === []
                || $this->haversine->distanceMeters($normalizedCoordinates[array_key_last($normalizedCoordinates)], $normalizedCoordinate) > 0) {
                $normalizedCoordinates[] = $normalizedCoordinate;
            }
        }

        if (count($normalizedCoordinates) < 2) {
            return [
                'coordinates' => $normalizedCoordinates,
                'length_m' => 0.0,
                'samples' => [],
                'spacing_m' => $spacingMeters,
            ];
        }

        $segments = [];
        $routeLengthMeters = 0.0;

        for ($index = 0; $index < count($normalizedCoordinates) - 1; $index++) {
            $start = $normalizedCoordinates[$index];
            $end = $normalizedCoordinates[$index + 1];
            $segmentLengthMeters = $this->haversine->distanceMeters($start, $end);

            if ($segmentLengthMeters === 0.0) {
                continue;
            }

            $segments[] = [
                'start' => $start,
                'end' => $end,
                'start_distance_m' => $routeLengthMeters,
                'length_m' => $segmentLengthMeters,
            ];
            $routeLengthMeters += $segmentLengthMeters;
        }

        if ($routeLengthMeters === 0.0 || $segments === []) {
            return [
                'coordinates' => $normalizedCoordinates,
                'length_m' => 0.0,
                'samples' => [],
                'spacing_m' => $spacingMeters,
            ];
        }

        $sampleCount = min(
            $maximumSamples,
            max(2, (int) ceil($routeLengthMeters / $spacingMeters) + 1),
        );
        $actualSpacingMeters = $routeLengthMeters / ($sampleCount - 1);
        $samples = [];
        $segmentIndex = 0;

        for ($sampleIndex = 0; $sampleIndex < $sampleCount; $sampleIndex++) {
            $targetDistanceMeters = $sampleIndex * $actualSpacingMeters;

            while ($segmentIndex < count($segments) - 1
                && $targetDistanceMeters > $segments[$segmentIndex]['start_distance_m'] + $segments[$segmentIndex]['length_m']) {
                $segmentIndex++;
            }

            $segment = $segments[$segmentIndex];
            $segmentProgress = min(
                1,
                max(0, ($targetDistanceMeters - $segment['start_distance_m']) / $segment['length_m']),
            );

            $samples[] = [
                $segment['start'][0] + ($segment['end'][0] - $segment['start'][0]) * $segmentProgress,
                $segment['start'][1] + ($segment['end'][1] - $segment['start'][1]) * $segmentProgress,
            ];
        }

        return [
            'coordinates' => $normalizedCoordinates,
            'length_m' => $routeLengthMeters,
            'samples' => $samples,
            'spacing_m' => $actualSpacingMeters,
        ];
    }
}
