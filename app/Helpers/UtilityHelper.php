<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class UtilityHelper
{
    /**
     * Return script with nonce
     *
     * @param string $path
     *
     * @return string
     */
    public static function returnScriptWithNonce(string $path): string
    {
        return '<script nonce="' . csp_nonce('script') . '" src="' . $path . '"></script>';
    }

    /**
     * Create custom activity log.
     *
     * @param string $logName
     * @param string $description
     * @param ?Model $subject
     * @param ?array $properties
     *
     * @return Activity
     */
    public static function customActivityLog(
        string $logName,
        string $description,
        ?Model $subject = null,
        ?array $properties = null
    ): Activity {
        $activity = activity($logName);

        if ($subject) {
            $activity->performedOn($subject);
        }

        if (!empty($properties)) {
            $activity->withProperties($properties);
        }

        return $activity->log($description);
    }

    /**
     * Format date time.
     *
     * @param mixed $dateTime
     * @param ?string $format
     *
     * @return string
     */
    public static function formatDateTime(
        mixed $dateTime,
        ?string $format = null
    ): string {
        if (empty($dateTime)) {
            return '';
        }

        $format ??= config('constants.date_format.admin_display');

        $timezone = auth()->user()?->timezone
            ?? session('user_timezone')
            ?? config('app.timezone', 'UTC');

        return Carbon::parse($dateTime)
            ->timezone($timezone)
            ->format($format);
    }
}
