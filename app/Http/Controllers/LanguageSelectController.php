<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\LocalizationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LanguageSelectController extends Controller
{
    public function __construct(private LocalizationService $localization)
    {
    }

    public function index(): View
    {
        return view('language-select', [
            'languages' => $this->localization->getLocalizedNames(),
        ]);
    }

    public function update(string $lang)
    {
        if (!$this->localization->hasLanguageCode($lang)) {
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
