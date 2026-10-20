<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesInput;
use Illuminate\Foundation\Http\FormRequest;

class StoreEntretienRequest extends FormRequest
{
    use NormalizesInput;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeStrings(['titre', 'type', 'description', 'notes']);
    }

    public function rules(): array
    {
        return [
            'titre' => ['bail', 'required', 'string', 'min:2', 'max:150'],
            'type' => ['bail', 'required', 'string', 'min:2', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'date_prevue' => ['required', 'date'],
            'date_realisee' => ['nullable', 'date', 'after_or_equal:date_prevue'],
            'rappel_avant_jours' => ['nullable', 'integer', 'between:0,365'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
