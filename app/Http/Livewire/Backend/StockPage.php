<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class StockPage extends BackendPage
{
    use AuthorizesRequests;

    protected string $title = 'Stock';

    public function mount(): void
    {
        $this->authorize('manage stock');
    }

    public function render(): View
    {
        return parent::view('livewire.backend.stock-page', [
            'products' => Product::query()
                ->orderBy('category->'.config('app.fallback_locale'))
                ->orderBy('sequence')
                ->orderBy('name->'.config('app.fallback_locale'))
                ->get(),
        ]);
    }
}
