<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Support\ShoppingBasket;
use Illuminate\Support\Collection;
use Livewire\Component;

class ShopFrontPage extends Component
{
    public Collection $products;
    public Collection $categories;

    public function mount()
    {
        $this->products = Product::query()
            ->available()
            ->get()
            ->filter(fn ($product) => $product->quantity_available_for_customer > 0)
            ->sortBy('name');

        $this->categories = $this->products->values()
            ->sortBy('category')
            ->pluck('category')
            ->unique()
            ->values();
    }

    public function render(ShoppingBasket $basket)
    {
        return view('livewire.shop-front-page', [
                'basket' => $basket->items(),
            ])
            ->layout(null, ['title' => __('Choose your items')]);
    }

    public function add(ShoppingBasket $basket, $productId, $quantity = 1)
    {
        $basket->add($productId, $quantity);
    }
}
