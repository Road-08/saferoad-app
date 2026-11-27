<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RouteSafetyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouteScoreApiController extends Controller
{
    public function __construct(private RouteSafetyService $routeSafetyService)
    {
    }

    public function score(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'routes' => ['required', 'array', 'min:1', 'max:5'],
            'routes.*.id' => ['required'],
            'routes.*.coordinates' => ['required', 'array', 'min:2'],
            'routes.*.coordinates.*' => ['required', 'array', 'min:2'],
            'routes.*.duration' => ['nullable', 'numeric'],
            'routes.*.distance' => ['nullable', 'numeric'],
            'mode' => ['nullable', 'string', 'in:general,women,all'],
        ], [
            'routes.required' => 'Data rute harus disertakan.',
            'routes.*.coordinates.required' => 'Koordinat rute tidak boleh kosong.',
        ]);

        // Mode sepenuhnya ditentukan oleh server berdasarkan akses Women Zone user
        $user = $request->user();
        $mode = ($user && $user->canAccessWomenZone()) ? 'women' : 'general';

        $calculated = $this->routeSafetyService->calculateScores($validated['routes'], $mode);

        return response()->json([
            'success' => true,
            'mode' => $mode,
            'data' => $calculated,
            'disclaimer' => 'Skor adalah indikator keselamatan berdasarkan data fasilitas dan laporan komunitas.',
        ]);
    }
}
