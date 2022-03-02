<?php

namespace App\View\Components\Backend\Dashboard;

use App\Models\StockChange;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class StockChangeWidget extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if (Auth::user()->can('manage stock')) {
            return view('components.backend.dashboard.stock-change-widget', [
                'changes' => StockChange::query()
                    ->whereNull('order_id')
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get(),
            ]);
        }
        return '';
    }
}
