<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ShoppingBasket
{
    private Collection $items;

    private string $session_key = 'shopping-basket';

    public function __construct()
    {
        $this->items = collect(session()->get($this->session_key, []));
    }

    public function save()
    {
        $items = $this->items
            ->map(fn ($quantity) => intval($quantity))
            ->filter(fn ($quantity) => $quantity > 0);
        if ($items->isNotEmpty())  {
            session()->put($this->session_key, $items->toArray());
        } else {
            session()->forget($this->session_key);
        }
    }

    public function items(): Collection
    {
        return $this->items
            ->filter(fn ($quantity) => $quantity > 0);
    }

    public function get($productId): int
    {
        return $this->items[$productId] ?? 0;
    }

    public function add($productId, int $quantity)
    {
        $this->set($productId, $this->get($productId) + $quantity);
    }

    public function set($productId, int $quantity)
    {
        if ($quantity > 0) {
            $this->items[$productId] = $quantity;
            $this->save();
        } else {
            $this->remove($productId);
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
        $this->items->forget($productId);
        $this->save();
    }

    public function clear()
    {
        $this->items = collect();
        $this->save();
    }

    public function isNotEmpty(): bool
    {
        return $this->items()->isNotEmpty();
    }
}
