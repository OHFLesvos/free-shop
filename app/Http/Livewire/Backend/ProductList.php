<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProductList extends Component
{
    public Collection $products;

    public function mount()
    {
        $this->products = Product::query()
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.backend.product-list')
            ->layout('layouts.backend', ['title' => 'Products']);
    }
}
