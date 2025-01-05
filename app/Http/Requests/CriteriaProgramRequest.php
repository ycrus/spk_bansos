<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriteriaProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'criteriaProgram.*.weight' => ['required', 'numeric', 'min:0'],
            'criteriaProgram' => [function ($attribute, $value, $fail) {
                $totalWeight = collect($value)->sum('weight');
                if ($totalWeight > 100) {
                    $fail('The total weight of all criteria must not exceed 100%.');
                }
            }],
        ];
    }
}
