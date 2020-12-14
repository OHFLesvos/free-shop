<?php

namespace App\Http\Controllers;

class LanguageSelectController extends Controller
{
    public function index()
    {
        if (url()->previous() != route('languages')) {
            session()->flash('previous-url', url()->previous());
        }

        return view('language-select', [
            'languages' => config('app.supported_languages'),
        ]);
    }

    public function change(string $lang)
    {
        if (! isset(config('app.supported_languages')[$lang])) {
            $lang = config('app.fallback_locale');
        }

        session()->put('lang', $lang);

        if (session()->has('previous-url')) {
            return redirect(session()->get('previous-url'));
        }

        if (url()->previous() != route('languages')) {
            return redirect(url()->previous());
        }

        return redirect()->route('home');
    }
}
