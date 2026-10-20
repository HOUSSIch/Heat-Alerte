<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesInput;
use Illuminate\Foundation\Http\FormRequest;

class StoreAvisRequest extends FormRequest
{
    use NormalizesInput;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeStrings(['commentaire']);
    }

    public function rules(): array
    {
        return ['note' => ['bail', 'required', 'integer', 'between:1,5'], 'commentaire' => ['nullable', 'string', 'min:3', 'max:2000']];
    }
}
