<?php

namespace App\Http\Livewire\Backend;

use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\WithPagination;

class CustomerListPage extends BackendPage
{
    use AuthorizesRequests;
    use WithPagination;
    use WithSorting;

    protected string $title = 'Customers';

    protected string $paginationTheme = 'bootstrap';

    /**
     * @var array
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'tags' => ['except' => []],
    ];

    public string $search = '';

    public array $tags = [];

    public string $sortBy = 'name';

    public string $sortDirection = 'asc';

    protected array $sortableFields = [
        'name',
        'created_at',
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Customer::class);

        $this->search = request()->input('search', session()->get('customers.search', '')) ?? '';
        $this->tags = request()->input('tags', session()->get('customers.tags', [])) ?? [];
        session()->put('customers.tags', $this->tags);

        if (session()->has('customers.page')) {
            $this->setPage(session()->get('customers.page'));
        }
    }

    public function render(): View
    {
        session()->put('customers.page', $this->resolvePage());

        $customers = Customer::query()
            ->when(filled($this->search), fn ($qry) => $qry->filter(trim($this->search)))
            ->when(count($this->tags) > 0, function ($qry) {
                foreach ($this->tags as $tag) {
                    $qry->whereHas('tags', fn (Builder $query) => $query->whereSlug($tag));
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        $allTags = Tag::orderBy('name')
            ->has('customers')
            ->get();

        return parent::view('livewire.backend.customer-list-page', [
            'customers' => $customers,
            'allTags' => $allTags,
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(string $value): void
    {
        session()->put('customers.search', $value);
    }

    public function updatingTags(): void
    {
        $this->resetPage();
    }

    public function updatedTags(array $value): void
    {
        session()->put('customers.tags', $value);
    }
}
