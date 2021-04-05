<?php

namespace App\Http\Livewire\Backend;

use App\Models\TextBlock;
use App\Repository\TextBlockRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TextBlockListPage extends BackendPage
{
    use AuthorizesRequests;

    public function mount(TextBlockRepository $textRepo)
    {
        $this->authorize('viewAny', TextBlock::class);

        $textRepo->initialize();
    }

    protected $title = 'Text Blocks';

    public function render()
    {
        $blocks = TextBlock::query()
            ->whereIn('name', array_keys(config('text-blocks')))
            ->orderBy('name')
            ->get();
        return parent::view('livewire.backend.text-block-list-page', [
            'textBlocks' => collect(config('text-blocks'))
                ->keys()
                ->map(fn ($key) => $blocks->firstWhere('name', $key))
                ->filter(),
        ]);
    }
}
