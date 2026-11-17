<?php

namespace App\Http\Controllers;

use App\Models\Rute;
use Illuminate\Http\Request;

class RouteComparisonController extends Controller
{
    public function compare(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:2|max:3',
            'ids.*' => 'integer|exists:routes,route_id',
            'mode'  => 'nullable|string|in:general,women',
        ]);

        $mode = $validated['mode'] ?? 'general';
        $routes = Rute::whereIn('route_id', $validated['ids'])->get();

        $tercepat  = $routes->sortBy('duration')->first();
        $terpendek = $routes->sortBy('distance')->first();
        $teraman   = $routes->sortByDesc('security_score')->first();

        return response()->json([
            'mode'           => $mode,
            'recommended_id' => $teraman ? $teraman->route_id : null,
            'recommendation_label' => $mode === 'women' ? 'Paling Aman untuk Perempuan' : 'Rute Paling Aman',
            'routes' => $routes->map(fn ($r) => [
                'id'             => $r->route_id,
                'distance_km'    => (float) $r->distance,
                'duration_min'   => $r->duration,
                'security_score' => (float) $r->security_score,
                'security_level' => $r->security_level,
                'is_fastest'     => $tercepat && $r->route_id === $tercepat->route_id,
                'is_shortest'    => $terpendek && $r->route_id === $terpendek->route_id,
                'is_safest'      => $teraman && $r->route_id === $teraman->route_id,
                'is_pink'        => $mode === 'women' && $teraman && $r->route_id === $teraman->route_id,
                'color'          => ($mode === 'women' && $teraman && $r->route_id === $teraman->route_id) ? '#ec4899' : '#2563eb',
            ])->values(),
        ]);
    }
}
