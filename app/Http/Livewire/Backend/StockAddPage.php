<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\CurrentRouteName;
use App\Models\Product;
use App\Models\StockChange;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockAddPage extends BackendPage
{
    use AuthorizesRequests;
    use CurrentRouteName;

    protected string $title = 'Add Stock';

    public string $description = '';

    public array $selection = [];

    protected array $rules = [
        'selection' => [
            'array',
            'min:1',
        ],
        'selection.*' => [
            'integer',
            'min:1',
        ],
        'description' => [
            'required',
        ],
    ];

    public function mount(): void
    {
        $this->authorize('manage stock');
    }

    public function render(): View
    {
        return parent::view('livewire.backend.stock-add-page', [
            'products' => Product::query()
                ->available()
                ->orderBy('category->' . config('app.fallback_locale'))
                ->orderBy('sequence')
                ->orderBy('name->' . config('app.fallback_locale'))
                ->get(),
        ]);
    }

    public function submit()
    {
        $this->validate();

        foreach ($this->selection as $id => $quantity) {
            if ($quantity < 1) {
                continue;
            }

            $product = Product::find($id);
            if ($product === null) {
                continue;
            }

            $product->stock += $quantity;
            $product->save();

            $change = new StockChange();
            $change->quantity = $quantity;
            $change->total = $product->stock;
            $change->description = $this->description;
            $change->product()->associate($product);
            $change->user()->associate(Auth::user());
            $change->save();
        }

        session()->flash('message', 'Stock added.');

        return redirect()->route('backend.stock.changes');
    }
}
