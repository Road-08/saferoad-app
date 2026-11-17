<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangerZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDangerZoneController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $zones = DangerZone::query()
            ->when($request->filled('danger_level'), fn ($q) => $q->where('danger_level', $request->query('danger_level')))
            ->when($request->has('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $zones,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'danger_level' => ['required', Rule::in(['tinggi', 'sedang', 'rendah'])],
            'category' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:10', 'max:2000'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $zone = DangerZone::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Titik rawan berhasil didaftarkan.',
            'data' => $zone,
        ], 201);
    }

    public function show(DangerZone $dangerZone): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $dangerZone,
        ]);
    }

    public function update(Request $request, DangerZone $dangerZone): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'danger_level' => ['sometimes', 'required', Rule::in(['tinggi', 'sedang', 'rendah'])],
            'category' => ['sometimes', 'required', 'string', 'max:100'],
            'latitude' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['sometimes', 'required', 'integer', 'min:10', 'max:2000'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $dangerZone->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data titik rawan berhasil diperbarui.',
            'data' => $dangerZone,
        ]);
    }

    public function destroy(DangerZone $dangerZone): JsonResponse
    {
        $dangerZone->delete();

        return response()->json([
            'success' => true,
            'message' => 'Titik rawan berhasil dihapus.',
        ]);
    }
}
