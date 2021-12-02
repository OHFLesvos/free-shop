<?php

namespace App\Http\Middleware;

use App\Services\GeoBlockChecker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Torann\GeoIP\Facades\GeoIP;

class GeoBlockWhitelist
{
    protected GeoBlockChecker $geoBlockChecker;

    public function __construct(GeoBlockChecker $geoBlockChecker)
    {
      $this->geoBlockChecker = $geoBlockChecker;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->geoBlockChecker->isBlocked()) {
            abort(Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
        }

        return $next($request);
    }
}
