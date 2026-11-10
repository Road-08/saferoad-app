<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MapFacilitiesRequest;
use App\Http\Requests\MapHazardsRequest;
use App\Http\Requests\MapOverviewRequest;
use App\Http\Requests\MapPointRequest;
use App\Http\Resources\Map\FacilityFeatureResource;
use App\Http\Resources\Map\HazardFeatureResource;
use App\Http\Resources\Map\MapFeatureCollectionResource;
use App\Models\Report;
use App\Models\SecurityFacility;
use App\Services\Map\MapQueryService;
use Illuminate\Http\Resources\Json\JsonResource;

class MapController extends Controller
{
    public function facilities(MapFacilitiesRequest $request, MapQueryService $mapQueryService): MapFeatureCollectionResource
    {
        $facilities = $mapQueryService->facilities($request->validated());
        $features = $facilities->map(fn (SecurityFacility $facility): FacilityFeatureResource => new FacilityFeatureResource($facility));

        return new MapFeatureCollectionResource($features);
    }

    public function hazards(MapHazardsRequest $request, MapQueryService $mapQueryService): MapFeatureCollectionResource
    {
        $hazards = $mapQueryService->hazards($request->validated());
        $features = $hazards->map(fn (Report $hazard): HazardFeatureResource => new HazardFeatureResource($hazard));

        return new MapFeatureCollectionResource($features);
    }

    public function point(MapPointRequest $request, MapQueryService $mapQueryService): JsonResource
    {
        $point = $request->validated();

        if ($point['type'] === 'facility') {
            return new FacilityFeatureResource($mapQueryService->facility((int) $point['id']));
        }

        return new HazardFeatureResource($mapQueryService->hazard((int) $point['id']));
    }

    public function overview(MapOverviewRequest $request, MapQueryService $mapQueryService): MapFeatureCollectionResource
    {
        $points = $mapQueryService->overview($request->validated());
        $features = $points->map(function (SecurityFacility|Report $point): JsonResource {
            if ($point instanceof SecurityFacility) {
                return new FacilityFeatureResource($point);
            }

            return new HazardFeatureResource($point);
        });

        return new MapFeatureCollectionResource($features);
    }
}
