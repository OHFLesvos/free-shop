<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Livewire\Component;

class OrderLookup extends Component
{
    public string $customer_id_number = '';
    public string $customer_phone = '';
    public $results = null;

    protected $rules = [
        'customer_id_number' => 'required',
        'customer_phone' => 'required',
    ];

    protected $validationAttributes = [
        'customer_id_number' => 'ID number',
        'customer_phone' => 'phone number',
    ];

    public function render()
    {
        return view('livewire.order-lookup')
            ->layout(null, ['title' => 'Find your order']);
    }

    public function submit()
    {
        $this->validate();

        $this->results = Order::query()
            ->whereNumberCompare('customer_id_number', $this->customer_id_number)
            ->whereNumberCompare('customer_phone', $this->customer_phone)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
