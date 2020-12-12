<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Livewire\Component;

class OrderLookupPage extends Component
{
    public string $id_number = '';
    public string $phone = '';
    public $results = null;

    protected $queryString = [
        'id_number' => ['except' => ''],
        'phone' => ['except' => ''],
    ];

    protected $rules = [
        'id_number' => 'required',
        'phone' => [
            'required',
            'phone:AUTO'
        ],
    ];

    protected $validationAttributes = [
        'id_number' => 'ID number',
        'phone' => 'phone number',
    ];

    public function mount()
    {
        if (filled($this->id_number) && filled($this->phone)) {
            $this->results = $this->search($this->id_number, $this->phone);
        }
    }

    public function render()
    {
        return view('livewire.order-lookup-page')
            ->layout(null, ['title' => __('Find your order')]);
    }

    public function submit()
    {
        $this->validate();

        $this->results = $this->search($this->id_number, $this->phone);
    }

    private function search($id_number, $phone)
    {
        return Order::query()
            ->whereNumberCompare('customer_id_number', $id_number)
            ->whereNumberCompare('customer_phone', $phone)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
