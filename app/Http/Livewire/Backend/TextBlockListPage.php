<?php

namespace App\Http\Livewire\Backend;

use App\Http\Livewire\Traits\CurrentRouteName;
use App\Models\TextBlock;
use App\Repository\TextBlockRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class TextBlockListPage extends BackendPage
{
    use AuthorizesRequests;
    use CurrentRouteName;

    protected string $title = 'Text Blocks';

    public function mount(TextBlockRepository $textRepo): void
    {
        $this->authorize('viewAny', TextBlock::class);

        $textRepo->initialize();
    }

    public function render(): View
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
