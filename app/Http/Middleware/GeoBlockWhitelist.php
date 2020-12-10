<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GeoBlockWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $countries = setting()->get('geoblock.whitelist', []);
        if (count($countries) > 0 && ! in_array(geoip()->getLocation()['iso_code'], $countries)) {
            abort(Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
        }

        return $next($request);
    }
}
