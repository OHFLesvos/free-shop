<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LanguageSelectController extends Controller
{
    private Collection $languages;

    public function __construct()
    {
        $this->languages = collect(config('localization.languages'));
    }

    public function index(): View
    {
        return view('language-select', [
            'languages' => $this->languages->pluck('name_localized', 'code'),
        ]);
    }

    public function update(string $lang)
    {
        if ($this->languages->where('code', $lang)->isEmpty()) {
            $lang = config('app.fallback_locale');
        }

        session()->put('lang', $lang);

        $customer = Auth::guard('customer')->user();
        if ($customer != null && $customer instanceof Customer) {
            $customer->locale = $lang;
            $customer->save();
        }

        if (session()->has('requested-url')) {
            return redirect(session()->pull('requested-url'));
        }

        if (url()->previous() != route('languages')) {
            return redirect(url()->previous());
        }

        return redirect()->route('home');
    }
}
