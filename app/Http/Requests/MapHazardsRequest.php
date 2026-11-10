<?php

namespace App\Http\Requests;

class MapHazardsRequest extends MapQueryRequest
{
    public function rules(): array
    {
        return [
            'bbox' => $this->bboxRules(),
            'category' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return parent::messages() + [
            'category.string' => 'Kategori harus berupa teks.',
            'category.max' => 'Kategori maksimal 255 karakter.',
        ];
    }
}
