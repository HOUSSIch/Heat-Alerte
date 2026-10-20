<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicationQuartierRequest extends FormRequest
{
    use NormalizesInput;

    public function authorize(): bool
    {
        return $this->user()?->quartier_id !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeStrings(['titre', 'contenu']);
    }

    public function rules(): array
    {
        return ['titre' => ['bail', 'required', 'string', 'min:5', 'max:150'], 'contenu' => ['bail', 'required', 'string', 'min:10', 'max:5000'], 'type' => ['bail', 'required', Rule::in(['probleme', 'information', 'entraide', 'alerte'])]];
    }
}
