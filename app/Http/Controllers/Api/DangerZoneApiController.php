<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DangerZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DangerZoneApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $zones = DangerZone::query()->active()->get()->map(function (DangerZone $zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'danger_level' => $zone->danger_level,
                'category' => $zone->category,
                'latitude' => $zone->latitude,
                'longitude' => $zone->longitude,
                'radius_meters' => $zone->radius_meters,
                'description' => $zone->description,
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $zones->count(),
            'data' => $zones,
        ]);
    }
}
