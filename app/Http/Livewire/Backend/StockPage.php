<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StockPage extends BackendPage
{
    use AuthorizesRequests;

    public function mount()
    {
        $this->authorize('manage stock');
    }

    protected $title = 'Stock';

    public function render()
    {
        return parent::view('livewire.backend.stock-page', [
        'products' => Product::query()
            ->orderBy('category->' . config('app.fallback_locale'))
            ->orderBy('sequence')
            ->orderBy('name->' . config('app.fallback_locale'))
            ->get(),
        ]);
    }
}
