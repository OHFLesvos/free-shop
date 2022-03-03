<?php

namespace App\Http\Livewire\Components;

use App\Http\Livewire\Traits\TrimEmptyStrings;
use Livewire\Component;

class AddCommentInput extends Component
{
    use TrimEmptyStrings;

    public bool $showAddComment = false;

    public string $newComment = '';


    public function render()
    {
        return view('livewire.components.add-comment-input');
    }

    public function saveComment(): void
    {
        $this->validate([
            'newComment' => [
                'required',
                'string',
            ]
        ]);

        $this->emit('commentAdded', [
            'content' => $this->newComment,
        ]);

        $this->reset(['showAddComment', 'newComment']);
    }

    public function updatingShowAddComment(bool $value): void
    {
        if (!$value) {
            $this->reset(['newComment']);
        }
    }
}
