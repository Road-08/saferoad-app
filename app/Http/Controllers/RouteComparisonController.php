<?php

namespace App\Http\Controllers;

use App\Models\Rute;
use Illuminate\Http\Request;

class RouteComparisonController extends Controller
{
    public function compare(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:2|max:3',
            'ids.*' => 'integer|exists:routes,route_id',
        ]);

        $routes = Rute::whereIn('route_id', $request->ids)->get();

        $tercepat  = $routes->sortBy('duration')->first();
        $terpendek = $routes->sortBy('distance')->first();
        $teraman   = $routes->sortByDesc('security_score')->first();

        return response()->json([
            'recommended_id' => $teraman->route_id,
            'routes' => $routes->map(fn ($r) => [
                'id'             => $r->route_id,
                'distance_km'    => (float) $r->distance,
                'duration_min'   => $r->duration,
                'security_score' => (float) $r->security_score,
                'security_level' => $r->security_level,
                'is_fastest'     => $r->route_id === $tercepat->route_id,
                'is_shortest'    => $r->route_id === $terpendek->route_id,
                'is_safest'      => $r->route_id === $teraman->route_id,
            ])->values(),
        ]);
    }
}
