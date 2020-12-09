<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;

class WelcomePage extends Component
{
    public $products;

    public function mount()
    {
        $this->products = Product::orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.welcome-page')
            ->layout(null, ['title' => 'Welcome']);
    }
}
