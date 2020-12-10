<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductEdit extends Component
{
    use WithFileUploads;

    public Product $product;

    public $picture;

    public Collection $categories;

    public bool $removePicture = false;

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
        $this->categories = Product::query()
            ->groupBy('category')
            ->select('category')
            ->orderBy('category')
            ->get()
            ->pluck('category');
    }

    public function render()
    {
        return view('livewire.backend.product-form')
            ->layout('layouts.backend', ['title' => 'Edit Product ' . $this->product->name]);
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

        if (isset($this->product->picture) && ($this->removePicture || isset($this->picture))) {
            $this->product->picture = null;
            Storage::delete($this->product->picture);
        }

        if (isset($this->picture)) {
            $this->product->picture = $this->picture->storePublicly('public/pictures');
        }

        $this->product->save();

        return redirect()->route('backend.products');
    }

    public function delete()
    {
        if ($this->product->orders->isNotEmpty()) {
            return;
        }

        if (isset($this->product->picture)) {
            Storage::delete($this->product->picture);
        }

        $this->product->delete();

        return redirect()->route('backend.products');
    }
}
