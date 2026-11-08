<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

abstract class MapQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<int, Closure(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('bbox')) {
                return;
            }

            $bbox = $this->input('bbox');

            if (! is_string($bbox) || $bbox === '') {
                return;
            }

            [$minLng, $minLat, $maxLng, $maxLat] = array_map('floatval', explode(',', $bbox));

            if ($minLng < -180 || $maxLng > 180 || $minLat < -90 || $maxLat > 90 || $minLng >= $maxLng || $minLat >= $maxLat) {
                $validator->errors()->add('bbox', 'Nilai bbox harus berada dalam rentang koordinat yang valid dan batas minimum harus lebih kecil dari batas maksimum.');
            }
        }];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bbox.string' => 'Parameter bbox harus berupa teks.',
            'bbox.regex' => 'Format bbox harus minLng,minLat,maxLng,maxLat dengan angka koordinat yang valid.',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function bboxRules(): array
    {
        return [
            'nullable',
            'string',
            'regex:/\A-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?\z/',
        ];
    }
}
