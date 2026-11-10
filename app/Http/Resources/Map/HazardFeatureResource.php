<?php

namespace App\Http\Resources\Map;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HazardFeatureResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    (float) $this->location->longitude,
                    (float) $this->location->latitude,
                ],
            ],
            'properties' => [
                'id' => $this->report_id,
                'category' => $this->category,
                'event_time' => $this->event_time?->toISOString(),
                'description' => $this->description,
                'address' => $this->location->address,
            ],
        ];
    }
}
