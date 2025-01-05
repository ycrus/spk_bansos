<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TotalWeightRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }

    public function passes($attribute, $value)
    {
        // Total semua weight dalam Repeater
        $totalWeight = array_sum(array_column($value, 'weight'));
        return $totalWeight <= 100;
    }

    public function message()
    {
        return 'Total bobot tidak boleh lebih dari 100%.';
    }
}
