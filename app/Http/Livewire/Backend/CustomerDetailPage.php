<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;

class CustomerDetailPage extends BackendPage
{
    use WithPagination;
    use AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public Customer $customer;

    protected function title()
    {
        return 'Customer ' . $this->customer->name;
    }

    public function render()
    {
        $this->authorize('view', $this->customer);

        return parent::view('livewire.backend.customer-detail-page', [
            'orders' => $this->customer->orders()
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ]);
    }
}
