<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\WithPagination;

class CustomerListPage extends BackendPage
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->search = request()->input('search', session()->get('customers.search', '')) ?? '';
    }

    protected $title = 'Customers';

    public function render()
    {
        return parent::view('livewire.backend.customer-list-page', [
            'customers' => Customer::query()
                ->when(filled($this->search), fn ($qry) => $qry->filter(trim($this->search)))
                ->orderBy('name')
                ->paginate(10),
            ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch($value)
    {
        session()->put('customers.search', $value);
    }
}
