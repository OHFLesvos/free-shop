<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ShoppingBasket
{
    private Collection $items;

    private const SESSION_KEY = 'shopping-basket';

    public function __construct()
    {
        $this->items = collect(session()->get(self::SESSION_KEY, []));
    }

    public function save(): void
    {
        $items = $this->items
            ->map(fn ($quantity) => intval($quantity))
            ->filter(fn ($quantity) => $quantity > 0);

        if ($items->isNotEmpty()) {
            session()->put(self::SESSION_KEY, $items->toArray());

            return;
        }
        session()->forget(self::SESSION_KEY);
    }

    public function items(): Collection
    {
        return $this->items
            ->filter(fn ($quantity) => $quantity > 0);
    }

    public function get(int $productId): int
    {
        return $this->items[$productId] ?? 0;
    }

    public function add(int $productId, int $quantity): void
    {
        $this->set($productId, $this->get($productId) + $quantity);
    }

    public function set(int $productId, int $quantity): void
    {
        if ($quantity > 0) {
            $this->items[$productId] = $quantity;
            $this->save();

            return;
        }
        $this->remove($productId);
    }

    public function decrease(int $productId): void
    {
        $this->add($productId, -1);
    }

    public function increase(int $productId): void
    {
        $this->add($productId, 1);
    }

    public function remove(int $productId): void
    {
        $this->items->forget((string) $productId);
        $this->save();
    }

    public function clear(): void
    {
        $this->items = collect();
        $this->save();
    }

    public function isNotEmpty(): bool
    {
        return $this->items()->isNotEmpty();
    }
}
