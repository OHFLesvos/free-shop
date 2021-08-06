<?php

namespace App\Http\Livewire\Backend;

use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class ManageTagsPage extends BackendPage
{
    use AuthorizesRequests;

    protected string $title = 'Manage Tags';

    public string $newTagName = '';

    protected $rules = [
        'newTagName' => [
            'required',
            'filled',
            'unique:tags,name',
        ]
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Tag::class);
    }

    public function render(): View
    {
        return parent::view('livewire.backend.manage-tags-page', [
            'tags' => Tag::orderBy('name')
                ->with('customers')
                ->get(),
        ]);
    }

    public function submit()
    {
        $this->validate();

        Tag::create([
            'name' => $this->newTagName,
        ]);

        $this->reset();

        $this->emit('tagAdded');
    }
}
