<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Livewire\Component;

class ProductEdit extends Component
{
    public Product $product;

    protected $rules = [
        'product.name' => 'required',
        'product.category' => 'required',
        'product.description' => 'nullable',
        'product.stock_amount' => 'integer|min:0',
        'product.customer_limit' => 'nullable|integer|min:0',
    ];

    public function render()
    {
        return view('livewire.backend.product-edit')
            ->layout('layouts.backend', ['title' => 'Edit Product ' . $this->product->name]);
    }

    public function submit()
    {
        $this->validate();

        $this->product->save();

        return redirect()->route('backend.products');
    }
}
