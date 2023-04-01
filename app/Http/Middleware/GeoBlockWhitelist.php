<?php

namespace App\Http\Middleware;

use App\Services\GeoBlockChecker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GeoBlockWhitelist
{
    public function __construct(
        protected GeoBlockChecker $geoBlockChecker
    ) {
    }

    /**
     * Handle an incoming request.
     *
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
