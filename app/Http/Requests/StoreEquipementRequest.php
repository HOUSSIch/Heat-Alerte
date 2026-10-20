<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesInput;
use Illuminate\Foundation\Http\FormRequest;

class StoreEquipementRequest extends FormRequest
{
    use NormalizesInput;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeStrings(['nom', 'type', 'marque', 'description']);
    }

    public function rules(): array
    {
        return [
            'nom' => ['bail', 'required', 'string', 'min:2', 'max:120'],
            'type' => ['bail', 'required', 'string', 'min:2', 'max:100'],
            'marque' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sensible_chaleur' => ['nullable', 'boolean'],
            'sensible_coupure' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function attributes(): array
    {
        return ['nom' => 'nom', 'type' => 'type', 'marque' => 'marque', 'description' => 'description', 'photo' => 'image'];
    }
}
