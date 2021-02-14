<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;

class CustomerListPage extends BackendPage
{
    use WithPagination;
    use AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('viewAny', Customer::class);

        $this->search = request()->input('search', session()->get('customers.search', '')) ?? '';

        if (session()->has('customers.page')) {
            $this->setPage(session()->get('customers.page'));
        }
    }

    protected $title = 'Customers';

    public function render()
    {
        session()->put('customers.page', $this->resolvePage());

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
