<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StockPage extends BackendPage
{
    use AuthorizesRequests;

    protected $title = 'Stock';

    public ?int $productId = null;
    public $quantity = null;

    protected $rules = [
        'quantity' => [
            'required',
            'integer',
            'min:0',
        ],        
    ];

    public function mount() 
    {
        $this->authorize('manage stock');
    }

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

    public function startEdit($id, $quantity)
    {
        if ($this->productId != $id) {
            $this->productId = $id;
            $this->quantity = $quantity;
            $this->emit('startEdit');
        }
    }

    public function cancelEdit()
    {
        $this->reset(['productId', 'quantity']);
    }

    public function submitEdit()
    {
        $this->validate();

        $product = Product::find($this->productId);
        if (isset($product)) {
            $product->stock = $this->quantity;
            if ($product->isDirty()) {
                $product->save();
                $this->emit('finishEdit', "Stock of '$product->name' set to $product->stock.");
            }
        }

        $this->reset(['productId', 'quantity']);
    }
}
