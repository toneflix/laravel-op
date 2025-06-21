<?php

namespace App\Helpers;

final class Transformer
{
    /**
     * Format date
     *
     * @param \Illuminate\Support\Carbon $date
     * @return void
     */
    public static function formatDate(\Illuminate\Support\Carbon $date, string $format = 'MMM DD, YYYY')
    {
        return $date->isoFormat($format);
    }

    public static function stringReplace(string $str, string $search, string $replace, bool $caseSensitive = true)
    {
        return str($str)->replace($search, $replace, $caseSensitive);
    }
}
