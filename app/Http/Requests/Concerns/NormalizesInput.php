<?php

namespace App\Http\Requests\Concerns;

trait NormalizesInput
{
    protected function normalizeStrings(array $fields): void
    {
        $normalized = [];

        foreach ($fields as $field) {
            if (! is_string($this->input($field))) {
                continue;
            }

            $value = trim($this->input($field));
            $normalized[$field] = $value === '' ? null : $value;
        }

        $this->merge($normalized);
    }
}
