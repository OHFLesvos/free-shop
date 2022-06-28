<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerHistory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public Customer $customer;

    public function render()
    {
        return view('livewire.backend.audit-history', [
            'audits' => $this->customer
                ->audits()
                ->with('user')
                ->paginate(10),
            'label' => 'customer',
        ]);
    }
}
