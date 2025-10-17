<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UniquePassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if password matches any existing user password
        $users = User::all();
        
        foreach ($users as $user) {
            if (Hash::check($value, $user->password)) {
                $fail('The password has already been used by another user. Please choose a different password.');
                return;
            }
        }
    }
}
