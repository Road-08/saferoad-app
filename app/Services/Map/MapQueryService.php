<?php

namespace App\Services\Map;

use App\Models\Report;
use App\Models\SecurityFacility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class MapQueryService
{
    /**
     * @param  array{type?: string|null, bbox?: string|null}  $filters
     * @return EloquentCollection<int, SecurityFacility>
     */
    public function facilities(array $filters): EloquentCollection
    {
        $query = SecurityFacility::query()
            ->select(['facility_id', 'location_id', 'facility_type', 'facility_name', 'description', 'status'])
            ->with('location')
            ->orderBy('facility_id');

        if (! empty($filters['type'])) {
            $query->whereIn('facility_type', explode(',', $filters['type']));
        }

        $this->applyBbox($query, $filters['bbox'] ?? null);

        return $query->limit(500)->get();
    }

    /**
     * @param  array{bbox?: string|null, category?: string|null}  $filters
     * @return EloquentCollection<int, Report>
     */
    public function hazards(array $filters): EloquentCollection
    {
        $query = $this->hazardQuery($filters['bbox'] ?? null)
            ->orderBy('report_id');

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->limit(500)->get();
    }

    /**
     * @param  array{bbox?: string|null}  $filters
     * @return Collection<int, SecurityFacility|Report>
     */
    public function overview(array $filters): Collection
    {
        $bbox = $filters['bbox'] ?? null;
        $facilities = SecurityFacility::query()
            ->select(['facility_id', 'location_id', 'facility_type', 'facility_name', 'description', 'status'])
            ->with('location')
            ->orderBy('facility_id');

        $this->applyBbox($facilities, $bbox);

        $hazards = $this->hazardQuery($bbox)
            ->orderBy('report_id');

        return collect($facilities->limit(250)->get())
            ->concat($hazards->limit(250)->get())
            ->values();
    }

    public function facility(int $id): SecurityFacility
    {
        return SecurityFacility::query()
            ->with('location')
            ->findOrFail($id);
    }

    public function hazard(int $id): Report
    {
        return $this->hazardQuery()
            ->findOrFail($id);
    }

    private function hazardQuery(?string $bbox = null): Builder
    {
        $query = Report::query()
            ->select(['report_id', 'location_id', 'category', 'event_time', 'description', 'status'])
            ->with('location')
            ->where('status', 'diteruskan_ke_keamanan');

        $this->applyBbox($query, $bbox);

        return $query;
    }

    private function applyBbox(Builder $query, ?string $bbox): void
    {
        if ($bbox === null || $bbox === '') {
            return;
        }

        [$minLng, $minLat, $maxLng, $maxLat] = array_map('floatval', explode(',', $bbox));

        $query->whereHas('location', function (Builder $locationQuery) use ($minLng, $minLat, $maxLng, $maxLat): void {
            $locationQuery
                ->whereBetween('longitude', [$minLng, $maxLng])
                ->whereBetween('latitude', [$minLat, $maxLat]);
        });
    }
}
