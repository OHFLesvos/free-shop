<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class CustomerDetailPage extends BackendPage
{
    use AuthorizesRequests;

    public Customer $customer;

    public string $tab = 'overview';

    protected $queryString = [
        'tab' => ['except' => 'overview'],
    ];

    public array $tabs = [
        'overview' => 'Overview',
        'comments' => 'Comments',
        'orders' => 'Orders',
        'history' => 'History',
    ];

    protected function title(): string
    {
        return 'Customer '.$this->customer->name;
    }

    public function render(): View
    {
        $this->authorize('view', $this->customer);

        return parent::view('livewire.backend.customer-detail-page');
    }
}
