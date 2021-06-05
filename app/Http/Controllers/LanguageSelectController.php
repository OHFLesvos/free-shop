<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LanguageSelectController extends Controller
{
    public function index(): View
    {
        return view('language-select', [
            'languages' => config('app.supported_languages'),
        ]);
    }

    public function change(string $lang): RedirectResponse
    {
        if (! isset(config('app.supported_languages')[$lang])) {
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
