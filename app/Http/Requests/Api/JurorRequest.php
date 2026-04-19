<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class JurorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('juror')?->id;
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . ($userId ?? 'NULL')],
            'password' => [$userId ? 'nullable' : 'required', 'string', 'min:8'],
            'selectedContests' => ['nullable', 'array'],
            'selectedContests.*' => ['exists:contests,id'],
        ];
    }
}
