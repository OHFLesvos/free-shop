<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;

class CustomerListPage extends BackendPage
{
    use WithPagination;
    use AuthorizesRequests;
    use WithSorting;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $tag = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'tag' => ['except' => ''],
    ];

    public string $sortBy = 'name';
    public string $sortDirection  = 'asc';
    protected $sortableFields = [
        'name',
        'created_at',
    ];

    public function mount()
    {
        $this->authorize('viewAny', Customer::class);

        $this->search = request()->input('search', session()->get('customers.search', '')) ?? '';
        $this->tag = request()->input('tag', session()->get('customers.tag', '')) ?? '';
        session()->put('customers.tag', $this->tag);

        if (session()->has('customers.page')) {
            $this->setPage(session()->get('customers.page'));
        }
    }

    protected $title = 'Customers';

    public function render()
    {
        session()->put('customers.page', $this->resolvePage());

        $customers = Customer::query()
            ->when(filled($this->search), fn ($qry) => $qry->filter(trim($this->search)))
            ->when(filled($this->tag), fn ($qry) => $qry->whereHas('tags', function (Builder $query) {
                $query->whereSlug($this->tag);
            }))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        $tags = Tag::orderBy('name')
            ->has('customers')
            ->get();

        return parent::view('livewire.backend.customer-list-page', [
            'customers' => $customers,
            'tags' => $tags,
        ]);
    }

    public function setTag($tag)
    {
        $this->tag = $this->tag == $tag ? '' : $tag;
        session()->put('customers.tag', $this->tag);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch($value)
    {
        session()->put('customers.search', $value);
    }

    public function updatingTag()
    {
        $this->resetPage();
    }

    public function updatedTag($value)
    {
        session()->put('customers.tag', $value);
    }
}
