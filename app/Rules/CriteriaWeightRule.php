<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CriteriaWeightRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $total = collect($value)->sum(fn($item) => (float) ($item['weight'] ?? 0));
        if ($total !== 100.0) {
            $fail("Total bobot harus tepat 100%.");
        }
    }
}
