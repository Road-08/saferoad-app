<?php

namespace App\Http\Requests;

class MapFacilitiesRequest extends MapQueryRequest
{
    public function rules(): array
    {
        return [
            'type' => ['nullable', 'string', 'regex:/\A(?:cctv|penerangan|pos_keamanan|titik_aman)(?:,(?:cctv|penerangan|pos_keamanan|titik_aman))*\z/'],
            'bbox' => $this->bboxRules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return parent::messages() + [
            'type.string' => 'Filter type harus berupa teks.',
            'type.regex' => 'Filter type hanya boleh berisi cctv, penerangan, pos_keamanan, atau titik_aman yang dipisahkan koma.',
        ];
    }
}
