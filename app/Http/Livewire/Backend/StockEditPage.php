<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class StockEditPage extends BackendPage
{
    use AuthorizesRequests;

    public Product $product;
    public int $freeQuantity;

    protected array $rules = [
        'product.stock' => [
            'required',
            'integer',
            'min:0',
        ],
        'freeQuantity' => [
            'required',
            'integer',
        ],
    ];

    protected function title(): string
    {
        return 'Edit Stock of ' . $this->product->name;
    }

    public function mount(): void
    {
        $this->authorize('manage stock');

        $this->freeQuantity = $this->product->free_quantity;
    }

    public function render(): View
    {
        return parent::view('livewire.backend.stock-edit-page');
    }

    public function updatingProductStock($value)
    {
        if (is_numeric($value)) {
            $this->freeQuantity = $value - $this->product->reserved_quantity;
        }
    }

    public function updatingFreeQuantity($value)
    {
        if (is_numeric($value)) {
            $this->product->stock = $value + $this->product->reserved_quantity;
        }
    }

    public function submit()
    {
        $this->validate();

        if ($this->product->isDirty()) {
            $this->product->save();
            session()->flash('message', "Stock of '" . $this->product->name . "' set to " . $this->product->stock . ".");
        }

        return redirect()->route('backend.stock');
    }
}
