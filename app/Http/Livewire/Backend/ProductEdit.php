<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductEdit extends Component
{
    use WithFileUploads;

    public Product $product;

    public $picture;

    protected $rules = [
        'product.name' => 'required',
        'product.category' => 'required',
        'product.description' => 'nullable',
        'product.stock_amount' => 'integer|min:0',
        'product.customer_limit' => 'nullable|integer|min:0',
        'product.is_available' => 'boolean',
        'picture' => 'nullable|image|max:1920',
    ];

    public function render()
    {
        return view('livewire.backend.product-edit')
            ->layout('layouts.backend', ['title' => 'Edit Product ' . $this->product->name]);
    }

    public function submit()
    {
        $this->validate();

        if (isset($this->picture)) {
            if (isset($this->product->picture)) {
                Storage::delete($this->product->picture);
            }
            $this->product->picture = $this->picture->store('public/pictures');
        }

        $this->product->save();

        return redirect()->route('backend.products');
    }
}
