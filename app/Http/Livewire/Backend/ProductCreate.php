<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductCreate extends Component
{
    use WithFileUploads;

    public Product $product;

    public $picture;

    // public bool $removePicture = false;

    protected $rules = [
        'product.name' => 'required',
        'product.category' => 'required',
        'product.description' => 'nullable',
        'product.stock_amount' => 'required|integer|min:0',
        'product.customer_limit' => 'nullable|integer|min:0',
        'product.is_available' => 'boolean',
        'picture' => 'nullable|image|max:4096',
    ];

    public function mount()
    {
        $this->product = new Product();
        $this->product->is_available = true;
    }

    public function render()
    {
        return view('livewire.backend.product-form')
            ->layout('layouts.backend', ['title' => 'Register Product ']);
    }

    public function updatedPicture()
    {
        $this->validate([
            'picture' => 'nullable|image|max:4096',
        ]);
    }

    public function submit()
    {
        $this->validate();

        if (isset($this->picture)) {
            $this->product->picture = $this->picture->storePublicly('public/pictures');
        }

        $this->product->save();

        return redirect()->route('backend.products');
    }
}
