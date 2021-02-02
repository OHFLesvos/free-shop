<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Services\CurrentCustomer;
use App\Support\ShoppingBasket;
use Illuminate\Support\Collection;
use Livewire\Component;

class ShopFrontPage extends Component
{
    public Collection $products;
    public Collection $categories;
    public Customer $customer;

    public function mount(CurrentCustomer $currentCustomer)
    {
        $this->customer = $currentCustomer->get();

        $this->products = Product::query()
            ->available()
            ->orderBy('sequence')
            ->orderBy('name')
            ->get()
            ->filter(fn ($product) => $product->quantity_available_for_customer > 0);

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
