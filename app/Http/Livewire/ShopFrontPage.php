<?php

namespace App\Http\Livewire;

use App;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\ShoppingBasket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ShopFrontPage extends FrontendPage
{
    public Collection $products;
    public Collection $categories;
    public ?Customer $customer = null;
    public bool $shopDisabled;
    public bool $dailyOrdersMaxedOut = false;
    public bool $useCategories = false;

    protected function title()
    {
        return __('Choose your items');
    }

    public function mount()
    {
        $this->shopDisabled = setting()->has('shop.disabled');
        $this->useCategories = setting()->has('shop.group_products_by_categories');
        $this->dailyOrdersMaxedOut = $this->dailyOrdersMaxedOut();

        $this->customer = Auth::guard('customer')->user();

        $this->products = Product::query()
            ->available()
            ->orderBy('category->' . App::getLocale())
            ->orderBy('sequence')
            ->orderBy('name->' . App::getLocale())
            ->get()
            ->filter(fn ($product) => $product->quantity_available_for_customer > 0);

        if ($this->useCategories) {
            $this->categories = $this->products->values()
                ->sortBy('category')
                ->pluck('category')
                ->unique()
                ->values();
        }
    }

    private function dailyOrdersMaxedOut(): bool
    {
        if (setting()->has('shop.max_orders_per_day') && setting()->get('shop.max_orders_per_day') > 0) {
            $currentOrderCount = Order::whereDate('created_at', today())
                ->where('status', '!=', 'cancelled')
                ->count();
            if (setting()->get('shop.max_orders_per_day') <= $currentOrderCount) {
                return true;
            }
        }
        return false;
    }

    public function render(ShoppingBasket $basket)
    {
        return parent::view('livewire.shop-front-page', [
            'basket' => $basket->items(),
            'nextOrderIn' => optional($this->customer)->getNextOrderIn(),
        ]);
    }

    public function add(ShoppingBasket $basket, $productId, $quantity = 1)
    {
        $price = $this->products->firstWhere('id', $productId)->price * $quantity;
        if ($price <= $this->AvailableCredit) {
            $basket->add($productId, $quantity);
        }
    }

    public function getAvailableCreditProperty(ShoppingBasket $basket)
    {
        $credit = $this->customer->credit;
        $credit -= $basket->items()
            ->map(fn ($quantity, $productId) => $this->products->firstWhere('id', $productId)->price * $quantity)
            ->sum();
        return $credit;
    }
}
