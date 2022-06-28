<?php

namespace App\Services;

use Torann\GeoIP\Facades\GeoIP;

class GeoBlockChecker
{
    public function isBlocked()
    {
        $countries = setting()->get('geoblock.whitelist', []);

        return count($countries) > 0 && ! in_array(GeoIP::getLocation()['iso_code'], $countries);
    }
}
