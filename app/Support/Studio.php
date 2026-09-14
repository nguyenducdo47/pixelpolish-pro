<?php

namespace App\Support;

use App\Enums\ContentProfile;

class Studio
{
    public static function home(?ContentProfile $profile = null): string
    {
        $url = '/studio/setup';

        if ($profile !== null) {
            return $url.'?profile='.$profile->value;
        }

        return $url;
    }
}
