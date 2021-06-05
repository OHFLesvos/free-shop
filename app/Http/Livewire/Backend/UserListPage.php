<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;

class UserListPage extends BackendPage
{
    use WithPagination;
    use AuthorizesRequests;

    protected string $title = 'Users';

    protected string $paginationTheme = 'bootstrap';

    /**
     * @var array
     */
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);

        $this->search = request()->input('search', session()->get('users.search', '')) ?? '';
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return parent::view('livewire.backend.user-list-page', [
            'users' => User::query()
                ->when(filled($this->search), fn ($qry) => $qry->filter(trim($this->search)))
                ->orderBy('name')
                ->paginate(10),
            ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(string $value): void
    {
        session()->put('users.search', $value);
    }
}
