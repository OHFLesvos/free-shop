<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;

class ProductListPage extends BackendPage
{
    public string $state;

    public function mount()
    {
        $this->state = session()->get('products.state', 'all');
    }

    protected $title = 'Products';

    public function render()
    {
        session()->put('products.state', $this->state);

        return parent::view('livewire.backend.product-list-page', [
            'products' => Product::query()
                ->when($this->state == 'available', fn ($qry) => $qry->available())
                ->when($this->state == 'disabled', fn ($qry) => $qry->disabled())
                ->orderBy('name->' . config('app.fallback_locale'))
                ->get(),
            ]);
    }
}
