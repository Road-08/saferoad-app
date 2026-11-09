<?php

namespace App\Http\Resources\Api;

use App\Services\SafetyScoreService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteScoreResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform route scores into the public API response without exposing model data.
     *
     * @return array{data: array<int, array<string, mixed>>, disclaimer: string}
     */
    public function toArray(Request $request): array
    {
        $factorOrder = ['hazard', 'lighting', 'cctv', 'security_post', 'crowd'];
        $data = collect($this->resource)
            ->map(function (array $route) use ($factorOrder): array {
                $factorsByKey = collect($route['factors'] ?? [])->keyBy('key');
                $factors = collect($factorOrder)
                    ->map(function (string $key) use ($factorsByKey): array {
                        $factor = $factorsByKey->get($key, []);
                        $factorScore = $factor['score'] ?? null;

                        return [
                            'key' => $key,
                            'label' => $factor['label'] ?? '',
                            'score' => $factorScore === null ? null : round((float) $factorScore, 1),
                            'weight' => round((float) ($factor['weight'] ?? 0), 4),
                            'available' => (bool) ($factor['available'] ?? false),
                            'note' => $factor['note'] ?? null,
                        ];
                    })
                    ->all();

                return [
                    'id' => $route['id'] ?? null,
                    'score' => $route['score'] ?? null,
                    'level' => $route['level'] ?? 'data_belum_cukup',
                    'level_label' => $route['level_label'] ?? 'Data belum cukup',
                    'note' => $route['note'] ?? null,
                    'factors' => $factors,
                ];
            })
            ->values()
            ->all();

        return [
            'data' => $data,
            'disclaimer' => SafetyScoreService::DISCLAIMER,
        ];
    }
}
