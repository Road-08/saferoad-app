<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MapPointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['facility', 'hazard'])],
            'id' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'type' => $this->route('type'),
            'id' => $this->route('id'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Tipe titik wajib diisi.',
            'type.string' => 'Tipe titik harus berupa teks.',
            'type.in' => 'Tipe titik hanya boleh facility atau hazard.',
            'id.required' => 'ID titik wajib diisi.',
            'id.integer' => 'ID titik harus berupa bilangan bulat.',
            'id.min' => 'ID titik harus lebih besar dari nol.',
        ];
    }
}
