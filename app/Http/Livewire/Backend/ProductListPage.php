<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class ProductListPage extends BackendPage
{
    use AuthorizesRequests;

    protected string $title = 'Products';

    public string $state;

    public function mount(): void
    {
        $this->authorize('viewAny', Product::class);

        $this->state = session()->get('products.state', 'all');
    }

    public function render(): View
    {
        session()->put('products.state', $this->state);

        return parent::view('livewire.backend.product-list-page', [
            'products' => Product::query()
                ->when($this->state == 'available', fn ($qry) => $qry->available())
                ->when($this->state == 'disabled', fn ($qry) => $qry->disabled())
                ->orderBy('category->'.config('app.fallback_locale'))
                ->orderBy('sequence')
                ->orderBy('name->'.config('app.fallback_locale'))
                ->get(),
        ]);
    }
}
