<?php

namespace App\Support;

use App\Models\Product;
use ErrorException;
use Illuminate\Support\Collection;

class ShoppingBasket
{
    private Collection $basket;
    private Collection $products;

    const SESSION_KEY = 'basket';

    public function __construct()
    {
        $this->products = Product::query()
            ->available()
            ->get()
            ->filter(fn ($product) => $product->quantity_available_for_customer > 0);

        $this->load();
    }

    public function load()
    {
        $savedBasket = collect(session()->get(self::SESSION_KEY, []));
        $this->basket = $savedBasket
            ->filter(fn ($val, $key) => $this->products->where('id', $key)->isNotEmpty());
    }

    public function save()
    {
        if ($this->basket->isNotEmpty())  {
            session()->put(self::SESSION_KEY, $this->basket
                ->map(fn ($quantity) => intval($quantity))
                ->filter(fn ($quantity) => $quantity > 0)
                ->toArray());
        } else {
            session()->forget(self::SESSION_KEY);
        }
    }

    public function items(): Collection
    {
        return $this->basket
            ->filter(fn ($quantity) => $quantity > 0);
    }

    public function get($productId): int
    {
        return $this->basket[$productId] ?? 0;
    }

    public function add($productId, int $quantity)
    {
        $this->set($productId, $this->get($productId) + $quantity);
    }

    public function set($productId, int $quantity)
    {
        if ($quantity > 0 && $quantity <= $this->products->where('id', $productId)->first()->quantity_available_for_customer) {
            $this->basket[$productId] = $quantity;
            $this->save();
        } else if ($quantity <= 0) {
            $this->remove($productId);
        } else {
            throw new ErrorException('Requested quantity is not available.');
        }
    }

    public function decrease($productId)
    {
        $this->add($productId, -1);
    }

    public function increase($productId)
    {
        $this->add($productId, 1);
    }

    public function remove($productId)
    {
        $this->basket->forget($productId);
        $this->save();
    }

    public function empty()
    {
        $this->basket = collect();
        $this->save();
    }
}
