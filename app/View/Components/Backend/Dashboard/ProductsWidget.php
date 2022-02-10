<?php

namespace App\View\Components\Backend\Dashboard;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class ProductsWidget extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if (Auth::user()->can('viewAny', Product::class)) {
            return view('components.backend.dashboard.products-widget', [
                'availableProducts' => Product::available()->count(),
            ]);
        }

        return '';
    }
}
