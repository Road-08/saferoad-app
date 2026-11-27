<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SafetyFacility;
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

        $facility->update($validated);

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
