<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

/**
 * Forces the use of the domain name stated in the APP_URL configuration.
 *
 * Inspired by https://robindirksen.com/blog/redirect-www-to-non-www-urls-in-laravel
 */
class RedirectToAppDomain
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('local') || app()->runningUnitTests()) {
            return $next($request);
        }

        $host = parse_url(config('app.url'), PHP_URL_HOST);
        if ($request->header('host') !== $host) {
            $request->headers->set('host', $host);

            return Redirect::to($request->path());
        }

        return $next($request);
    }
}
