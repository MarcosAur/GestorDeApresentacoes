<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ContestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:AGENDADO,EM_ANDAMENTO,FINALIZADO'],
            'selectedJurors' => ['nullable', 'array'],
            'selectedJurors.*' => ['exists:users,id'],
            'criteria' => ['required', 'array', 'min:1'],
            'criteria.*.name' => ['required', 'string', 'max:255'],
            'criteria.*.max_score' => ['required', 'numeric', 'min:0'],
            'criteria.*.weight' => ['required', 'numeric', 'min:0'],
            'criteria.*.tiebreak_priority' => ['nullable', 'integer'],
        ];
    }
}
