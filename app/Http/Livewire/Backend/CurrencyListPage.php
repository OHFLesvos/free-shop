<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\CurrentRouteName;
use App\Models\Currency;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class CurrencyListPage extends BackendPage
{
    use AuthorizesRequests;
    use CurrentRouteName;

    protected string $title = 'Currencies';

    public function mount(): void
    {
        $this->authorize('viewAny', Currency::class);
    }

    public function render(): View
    {
        $currencies = Currency::orderBy('name')->get();

        return parent::view('livewire.backend.currency-list-page', [
            'currencies' => $currencies,
        ]);
    }
}
