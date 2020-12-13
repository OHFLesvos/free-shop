<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerListPage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $status = 'open';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->search = request()->input('search', session()->get('customers.search', '')) ?? '';
    }

    public function render()
    {
        return view('livewire.backend.customer-list-page', [
            'customers' => Customer::query()
                ->when(filled($this->search), fn ($qry) => $qry->filter(trim($this->search)))
                ->orderBy('name')
                ->paginate(10),
            ])
            ->layout('layouts.backend', ['title' => 'Customers']);
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
