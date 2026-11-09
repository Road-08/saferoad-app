<?php

namespace App\Services\Geometry;

use InvalidArgumentException;

class Haversine
{
    /**
     * @param  array{0: float|int, 1: float|int}  $firstCoordinate
     * @param  array{0: float|int, 1: float|int}  $secondCoordinate
     */
    public function distanceMeters(array $firstCoordinate, array $secondCoordinate): float
    {
        if (count($firstCoordinate) < 2 || count($secondCoordinate) < 2) {
            throw new InvalidArgumentException('Koordinat harus berisi longitude dan latitude.');
        }

        [$firstLongitude, $firstLatitude] = array_map('floatval', array_slice($firstCoordinate, 0, 2));
        [$secondLongitude, $secondLatitude] = array_map('floatval', array_slice($secondCoordinate, 0, 2));

        if (! is_finite($firstLongitude) || ! is_finite($firstLatitude)
            || ! is_finite($secondLongitude) || ! is_finite($secondLatitude)
            || abs($firstLongitude) > 180 || abs($secondLongitude) > 180
            || abs($firstLatitude) > 90 || abs($secondLatitude) > 90) {
            throw new InvalidArgumentException('Koordinat berada di luar rentang geografis yang valid.');
        }

        $earthRadiusMeters = 6371000;
        $latitudeDifference = deg2rad($secondLatitude - $firstLatitude);
        $longitudeDifference = deg2rad($secondLongitude - $firstLongitude);
        $haversine = sin($latitudeDifference / 2) ** 2
            + cos(deg2rad($firstLatitude)) * cos(deg2rad($secondLatitude))
            * sin($longitudeDifference / 2) ** 2;
        $haversine = min(1, max(0, $haversine));

        return 2 * $earthRadiusMeters * asin(sqrt($haversine));
    }
}
