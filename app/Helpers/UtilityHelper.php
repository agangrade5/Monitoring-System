<?php

namespace App\Helpers;

class UtilityHelper
{
    public static function returnScriptWithNonce(string $path): string
    {
        return '<script nonce="' . csp_nonce('script') . '" src="' . $path . '"></script>';
    }
}
