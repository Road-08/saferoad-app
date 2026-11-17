<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SafetyFacility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SafetyFacilityApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SafetyFacility::query()->active();

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        $facilities = $query->get()->map(function (SafetyFacility $facility) {
            return [
                'id' => $facility->id,
                'name' => $facility->name,
                'type' => $facility->type,
                'latitude' => $facility->latitude,
                'longitude' => $facility->longitude,
                'address' => $facility->address,
                'status' => $facility->status,
                'description' => $facility->description,
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $facilities->count(),
            'data' => $facilities,
        ]);
    }
}
