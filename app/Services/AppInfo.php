<?php

namespace App\Services;

class AppInfo
{
    public static function basic()
    {
        return [
            'name' => 'Greyflix.io',
            'version' => env('APP_VERSION', config('app.api.version.code', '1.0.0')),
            'author' => 'Greysoft Technologies Ltd.',
            'updated' => env('LAST_UPDATE', '2022-11-02 00:27:53'),
        ];
    }

    public static function api()
    {
        return [
            'api' => self::basic(),
        ];
    }
}
