<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidMobile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove spaces, hyphens and brackets.
        $mobile = preg_replace('/[\s\-\(\)]/', '', $value);

        // Allow +91XXXXXXXXXX or 10-digit Indian mobile number.
        if (
            !preg_match('/^(?:\+91)?[6-9][0-9]{9}$/', $mobile)
        ) {
            $fail('validation.mobile_validation')->translate();
        }
    }
}