<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class ShopFrontPage extends Component
{
    public Collection $products;
    public Collection $categories;

    public array $basket;

    protected function rules() {
        $products = $this->products ?? Product::available()->get();
        $rules = [];
        foreach ($products as $product) {
            $rules['basket.' . $product->id] = [
                'integer',
                'min:0',
                'max:' . $product->available_for_customer_amount,
            ];
        }
        return $rules;
    }

    public function mount()
    {
        $this->products = Product::query()
            ->available()
            ->orderBy('name')
            ->get()
            ->filter(fn ($product) => $product->available_for_customer_amount > 0);

        $this->categories = $this->products->values()
            ->sortBy('category')
            ->pluck('category')
            ->unique()
            ->values();

        $savedBasket = session()->get('basket', []);
        $this->basket = $this->products
            ->mapWithKeys(fn ($product) => [$product->id => $savedBasket[$product->id] ?? 0])
            ->toarray();
    }

    public function render()
    {
        return view('livewire.shop-front-page')
            ->layout(null, ['title' => __('Choose your items')]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function getBasketContentsProperty()
    {
        return collect($this->basket)
            ->map(fn ($amount) => ltrim(preg_replace('/[^0-9]/', '', $amount), '0'))
            ->filter(fn ($amount) => $amount > 0)
            ->map(fn ($amount, $id) => [
                'name' => $this->products->where('id', $id)->first()->name,
                'amount' => $amount,
            ])
            ->sortBy('name');
    }

    public function checkout()
    {
        $this->validate();

        $this->storeBasket();

        return redirect()->route('checkout');
    }

    public function decrease($productId)
    {
        if ($this->basket[$productId] > 0)
        {
            $this->basket[$productId]--;
            $this->storeBasket();
        }
    }

    public function increase($productId)
    {
        if ($this->basket[$productId] < $this->products->where('id', $productId)->first()->available_for_customer_amount)
        {
            $this->basket[$productId]++;
            $this->storeBasket();
        }
    }

    private function storeBasket()
    {
        session()->put('basket', collect($this->basket)
            ->map(fn ($amount) => ltrim(preg_replace('/[^0-9]/', '', $amount), '0'))
            ->filter(fn ($amount) => $amount > 0)
            ->toArray());
    }
}
