<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SafetyFacility;
use App\Services\SafetyScoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminFacilityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $facilities = SafetyFacility::query()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->query('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $facilities,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['cctv', 'street_light', 'security_post'])],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive', 'maintenance'])],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $facility = SafetyFacility::create($validated);

        try {
            $scoreService = app(SafetyScoreService::class);
            $areaScore = $scoreService->calculateAreaScore((float) $facility->latitude, (float) $facility->longitude);
            $scoreService->recordScoreChange(
                targetType: 'area',
                targetIdentifier: "{$facility->latitude},{$facility->longitude}",
                scoreBefore: max(0, $areaScore['score'] - 5),
                scoreAfter: (float) $areaScore['score'],
                triggerEvent: 'facility_added',
                actorId: $request->user()?->id,
                actorRole: 'admin',
                details: ['facility_id' => $facility->id, 'type' => $facility->type],
                latitude: (float) $facility->latitude,
                longitude: (float) $facility->longitude
            );
        } catch (\Throwable $e) {
            // Ignore
        }

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas keamanan berhasil ditambahkan.',
            'data' => $facility,
        ], 201);
    }

    public function show(SafetyFacility $facility): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $facility,
        ]);
    }

    public function update(Request $request, SafetyFacility $facility): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::in(['cctv', 'street_light', 'security_post'])],
            'latitude' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive', 'maintenance'])],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $scoreBefore = null;
        try {
            $scoreService = app(SafetyScoreService::class);
            $areaBefore = $scoreService->calculateAreaScore((float) $facility->latitude, (float) $facility->longitude);
            $scoreBefore = (float) $areaBefore['score'];
        } catch (\Throwable $e) {
        }

        $facility->update($validated);

        try {
            $scoreService = app(SafetyScoreService::class);
            $areaAfter = $scoreService->calculateAreaScore((float) $facility->latitude, (float) $facility->longitude);
            $scoreService->recordScoreChange(
                targetType: 'area',
                targetIdentifier: "{$facility->latitude},{$facility->longitude}",
                scoreBefore: $scoreBefore ?? (float) $areaAfter['score'],
                scoreAfter: (float) $areaAfter['score'],
                triggerEvent: 'facility_updated',
                actorId: $request->user()?->id,
                actorRole: 'admin',
                details: ['facility_id' => $facility->id, 'type' => $facility->type],
                latitude: (float) $facility->latitude,
                longitude: (float) $facility->longitude
            );
        } catch (\Throwable $e) {
            // Ignore
        }

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas keamanan berhasil diperbarui.',
            'data' => $facility,
        ]);
    }

    public function destroy(SafetyFacility $facility): JsonResponse
    {
        $facility->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas keamanan berhasil dihapus.',
        ]);
    }
}
