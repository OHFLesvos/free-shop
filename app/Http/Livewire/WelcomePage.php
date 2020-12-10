<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class WelcomePage extends Component
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
            ->get();

        $this->categories = Product::query()
            ->available()
            ->groupBy('category')
            ->select('category')
            ->orderBy('category')
            ->get()
            ->pluck('category');

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
            ]);
    }

    public function checkout()
    {
        $this->validate();

        session()->put('basket', collect($this->basket)
            ->map(fn ($amount) => ltrim(preg_replace('/[^0-9]/', '', $amount), '0'))
            ->filter(fn ($amount) => $amount > 0)
            ->toArray());

        return redirect()->route('checkout');
    }

    public function increase($productId)
    {
        if ($this->basket[$productId] < $this->products->where('id', $productId)->first()->available_for_customer_amount)
        {
            $this->basket[$productId]++;
        }
    }
}
