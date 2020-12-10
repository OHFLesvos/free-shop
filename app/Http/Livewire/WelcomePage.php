<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class WelcomePage extends Component
{
    public Collection $products;
    public array $basket;

    public function mount()
    {
        $this->products = Product::query()
            ->available()
            ->orderBy('name')
            ->get();
        $savedBasket = session()->get('basket', []);
        $this->basket = $this->products
            ->mapWithKeys(fn ($product) => [$product->id => $savedBasket[$product->id] ?? 0])
            ->toarray();
    }

    public function render()
    {
        return view('livewire.welcome-page')
            ->layout(null, ['title' => 'Welcome']);
    }

    public function getBasketContentsProperty()
    {
        return collect($this->basket)
            ->map(fn ($amount) => ltrim(preg_replace('/[^0-9]/', '', $amount), '0'))
            ->filter(fn ($amount) => $amount > 0)
            ->map(fn ($amount, $id) => [
                'name' => $this->products->where('id', $id)->first()->name,
                'amount' => $amount,
            ]);
    }

    public function checkout()
    {
        session()->put('basket', collect($this->basket)
            ->map(fn ($amount) => ltrim(preg_replace('/[^0-9]/', '', $amount), '0'))
            ->filter(fn ($amount) => $amount > 0)
            ->toArray());

        return redirect()->route('checkout');
    }
}
