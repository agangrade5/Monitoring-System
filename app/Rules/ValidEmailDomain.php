<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidEmailDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $domain = strtolower(
            substr(strrchr($value, '@'), 1)
        );

        if (!$domain) {
            $fail('validation.email_domain')->translate();

            return;
        }

        // Reject obvious/example/test/local domains.
        $invalidDomains = [
            'example.com',
            'example.net',
            'example.org',
            'localhost',
            'local',
            'test',
            'invalid',
        ];

        if (in_array($domain, $invalidDomains, true)) {
            $fail('validation.email_validation')->translate();

            return;
        }

        // Check whether the domain has a mail server.
        if (
            !checkdnsrr($domain, 'MX') &&
            !checkdnsrr($domain, 'A')
        ) {
            $fail('validation.email_validation')->translate();
        }
    }
}
