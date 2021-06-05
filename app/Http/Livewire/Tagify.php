<?php

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Tagify extends Component
{
    public array $suggestions;

    public array $tags;

    public function mount(array $suggestions, ?array $tags = []): void
    {
        $this->suggestions = $suggestions;
        $this->tags = $tags;
    }

    public function changeTags(string $tags): void
    {
        if (empty($tags)) {
            $changed = [];
        } else {
            $changed = collect(json_decode($tags))->pluck('value')->toArray();
        }

        $this->emitUp('changeTags', $changed);
    }

    public function render(): View
    {
        return view('livewire.tagify', [
            'suggestions' => $this->suggestions,
        ]);
    }
}
