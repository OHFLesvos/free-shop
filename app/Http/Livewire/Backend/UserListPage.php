<?php

namespace App\Http\Livewire\Backend;

use App\Models\User;
use Livewire\WithPagination;

class UserListPage extends BackendPage
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->search = request()->input('search', session()->get('users.search', '')) ?? '';
    }

    protected $title = 'Users';

    public function render()
    {
        return parent::view('livewire.backend.user-list-page', [
            'users' => User::query()
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
        session()->put('users.search', $value);
    }
}
