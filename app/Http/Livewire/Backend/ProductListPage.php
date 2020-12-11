<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Livewire\Component;

class ProductListPage extends Component
{
    public string $state;

    public function mount()
    {
        $this->state = session()->get('products.state', 'all');
    }

    public function render()
    {
        session()->put('products.state', $this->state);

        return view('livewire.backend.product-list-page', [
            'products' => Product::query()
                ->when($this->state == 'available', fn ($qry) => $qry->available())
                ->when($this->state == 'disabled', fn ($qry) => $qry->disabled())
                ->orderBy('name')
                ->get(),
            ])
            ->layout('layouts.backend', ['title' => 'Products']);
    }
}
