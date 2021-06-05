<?php

namespace App\Util\Carbon;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserTimeZoneMixin
{
    public function toUserTimezone() {
        return static function () {
            $date = self::this();
            if (Auth::check() && filled(Auth::user()->timezone)) {
                return $date->timezone(Auth::user()->timezone);
            }
            if (setting()->has('timezone')) {
                return $date->timezone(setting()->get('timezone'));
            }
            return $date;
        };
    }
}
