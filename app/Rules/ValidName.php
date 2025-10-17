<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidName implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if name contains only numbers
        if (preg_match('/^[0-9]+$/', $value)) {
            $fail('The :attribute cannot contain only numbers.');
            return;
        }

        // Check if name contains at least one letter
        if (!preg_match('/[a-zA-Z]/', $value)) {
            $fail('The :attribute must contain at least one letter.');
            return;
        }

        // Check for invalid characters (only letters, spaces, hyphens, apostrophes, dots allowed)
        if (!preg_match('/^[a-zA-Z\s\'\-\.]+$/', $value)) {
            $fail('The :attribute may only contain letters, spaces, hyphens, apostrophes, and dots.');
            return;
        }
    }
}
