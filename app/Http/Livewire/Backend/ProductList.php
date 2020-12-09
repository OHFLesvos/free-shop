<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Livewire\Component;

class ProductList extends Component
{
    public $products;

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
