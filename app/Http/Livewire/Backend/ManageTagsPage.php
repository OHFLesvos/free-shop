<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\TrimEmptyStrings;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ManageTagsPage extends BackendPage
{
    use AuthorizesRequests;
    use TrimEmptyStrings;

    protected string $title = 'Manage Tags';

    public string $newTagName = '';

    public ?Tag $editTag = null;

    protected function rules()
    {
        return [
            'editTag.name' => [
                'required',
                'filled',
                Rule::unique('tags', 'name')->ignore($this->editTag->id),
            ],
        ];
    }

    public function mount(): void
    {
        $this->authorize('update customers');
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
        $this->validate([
            'newTagName' => [
                'required',
                'filled',
                'unique:tags,name',
            ],
        ]);

        Tag::create([
            'name' => $this->newTagName,
        ]);

        $this->reset();

        $this->emit('tagAdded');
    }

    public function edit(int $id)
    {
        $this->editTag = $id > 0 ? Tag::find($id) : null;
    }

    public function update()
    {
        $this->validate();

        $this->editTag->save();

        $this->reset();
    }

    public function delete(int $id)
    {
        Tag::destroy($id);

        $this->reset();
    }
}
