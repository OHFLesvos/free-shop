<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class StockPage extends BackendPage
{
    use AuthorizesRequests;

    protected string $title = 'Stock';

    public ?int $productId = null;
    public ?int $quantity = null;

    protected array $rules = [
        'quantity' => [
            'required',
            'integer',
            'min:0',
        ],
    ];

    public function mount(): void
    {
        $this->authorize('manage stock');
    }

    public function render(): View
    {
        return parent::view('livewire.backend.stock-page', [
            'products' => Product::query()
                ->orderBy('category->' . config('app.fallback_locale'))
                ->orderBy('sequence')
                ->orderBy('name->' . config('app.fallback_locale'))
                ->get(),
        ]);
    }

    public function startEdit(int $id, int $quantity): void
    {
        if ($this->productId != $id) {
            $this->productId = $id;
            $this->quantity = $quantity;
            $this->emit('startEdit');
        }
    }

    public function cancelEdit(): void
    {
        $this->reset(['productId', 'quantity']);
    }

    public function submitEdit(): void
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
