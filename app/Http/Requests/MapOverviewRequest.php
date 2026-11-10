<?php

namespace App\Http\Requests;

class MapOverviewRequest extends MapQueryRequest
{
    public function rules(): array
    {
        return [
            'bbox' => $this->bboxRules(),
        ];
    }
}
