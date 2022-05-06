<?php

namespace App\Http\Livewire\Components;

use App\Http\Livewire\Traits\TrimEmptyStrings;
use Livewire\Component;

class AddCommentInput extends Component
{
    use TrimEmptyStrings;

    public bool $isEditing = false;

    public string $content = '';

    public function render()
    {
        return view('livewire.components.add-comment-input');
    }

    public function saveComment(): void
    {
        $this->validate([
            'content' => [
                'required',
                'string',
            ]
        ]);

        $this->emit('commentAdded', $this->content);

        $this->reset(['isEditing', 'content']);
    }

    public function updatingIsEditing(bool $value): void
    {
        if (!$value) {
            $this->reset(['content']);
        }
    }
}
