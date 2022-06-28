<?php

namespace App\Http\Middleware;

use App\Services\LocalizationService;
use Closure;
use Illuminate\Http\Request;

class SetLanguage
{
    public function __construct(private LocalizationService $localization)
    {
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
        $lang = $request->input('lang', session()->get('lang'));
        if ($lang !== null && $this->localization->hasLanguageCode($lang)) {
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
