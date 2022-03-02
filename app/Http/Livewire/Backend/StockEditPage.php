<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use App\Models\StockChange;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockEditPage extends BackendPage
{
    use AuthorizesRequests;

    public Product $product;
    public $freeQuantity;
    public string $description;

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
        'description' => [
            'required',
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
        $this->description = strval(session()->get('stock.edit.description', ''));
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
            $quantity = $this->product->stock - $this->product->getOriginal('stock');

            $this->product->save();

            if ($quantity != 0) {
                $change = new StockChange();
                $change->quantity = $quantity;
                $change->total = $this->product->stock;
                $change->description = $this->description;
                $change->product()->associate($this->product);
                $change->user()->associate(Auth::user());
                $change->save();

                if (blank($this->description)) {
                    session()->forget('stock.edit.description');
                } else {
                    session()->put('stock.edit.description', trim($this->description));
                }
            }

            session()->flash('message', "Stock of '" . $this->product->name . "' set to " . $this->product->stock . ".");
        }

        return redirect()->route('backend.stock');
    }
}
