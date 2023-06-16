<?php

namespace App\Helpers;

use Carbon\Carbon;

class Date
{
    /**
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function toCarbonGregorianFormat(string $date, string $format): string
    {
        return Carbon::parse($date)->format($format);
    }
}
