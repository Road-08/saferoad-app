<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScoreRoutesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'routes' => [
                'required',
                'array',
                'min:1',
                'max:'.(int) config('safety.limits.max_routes', 3),
            ],
            'routes.*' => ['required', 'array:id,coordinates'],
            'routes.*.id' => ['sometimes', 'nullable', 'string'],
            'routes.*.coordinates' => [
                'required',
                'array',
                'min:2',
                'max:'.(int) config('safety.limits.max_points_per_route', 5000),
            ],
            'routes.*.coordinates.*' => ['required', 'array', 'size:2'],
            'routes.*.coordinates.*.0' => ['required', 'numeric', 'between:-180,180'],
            'routes.*.coordinates.*.1' => ['required', 'numeric', 'between:-90,90'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'routes.required' => 'Daftar rute wajib diisi.',
            'routes.array' => 'Daftar rute harus berupa array.',
            'routes.min' => 'Minimal satu rute harus dikirim.',
            'routes.max' => 'Jumlah rute melebihi batas maksimum.',
            'routes.*.required' => 'Data setiap rute wajib diisi.',
            'routes.*.array' => 'Setiap rute harus berupa objek dengan id dan coordinates.',
            'routes.*.id.string' => 'ID rute harus berupa teks.',
            'routes.*.coordinates.required' => 'Koordinat setiap rute wajib diisi.',
            'routes.*.coordinates.array' => 'Koordinat rute harus berupa array.',
            'routes.*.coordinates.min' => 'Setiap rute harus memiliki minimal dua titik koordinat.',
            'routes.*.coordinates.max' => 'Jumlah titik koordinat melebihi batas maksimum.',
            'routes.*.coordinates.*.required' => 'Setiap titik koordinat wajib diisi.',
            'routes.*.coordinates.*.array' => 'Setiap titik harus berupa pasangan longitude dan latitude.',
            'routes.*.coordinates.*.size' => 'Setiap titik harus berisi tepat dua angka: longitude dan latitude.',
            'routes.*.coordinates.*.0.required' => 'Longitude wajib diisi.',
            'routes.*.coordinates.*.0.numeric' => 'Longitude harus berupa angka.',
            'routes.*.coordinates.*.0.between' => 'Longitude harus berada antara -180 dan 180.',
            'routes.*.coordinates.*.1.required' => 'Latitude wajib diisi.',
            'routes.*.coordinates.*.1.numeric' => 'Latitude harus berupa angka.',
            'routes.*.coordinates.*.1.between' => 'Latitude harus berada antara -90 dan 90.',
        ];
    }
}
