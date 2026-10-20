<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesInput;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentairePublicationRequest extends FormRequest
{
    use NormalizesInput;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeStrings(['contenu']);
    }

    public function rules(): array
    {
        return ['contenu' => ['bail', 'required', 'string', 'min:2', 'max:2000']];
    }
}
