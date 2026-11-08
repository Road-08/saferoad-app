<?php

namespace App\Http\Resources\Map;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacilityFeatureResource extends JsonResource
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
                'id' => $this->facility_id,
                'type' => $this->facility_type,
                'name' => $this->facility_name,
                'description' => $this->description,
                'status' => $this->status,
            ],
        ];
    }
}
