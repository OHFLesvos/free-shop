<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLanguage
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
        $lang = $request->input('lang', session()->get('lang'));
        if ($lang !== null && isset(config('app.supported_languages')[$lang])) {
            app()->setLocale($lang);
            session()->put('lang', $lang);
        }
        if (! session()->has('lang')) {
            session()->put('requested-url', $request->fullUrl());

            return redirect()->route('languages');
        }

        return $next($request);
    }
}
