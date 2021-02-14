<?php

namespace App\Http\Livewire\Backend;

use App\Models\TextBlock;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TextBlockListPage extends BackendPage
{
    use AuthorizesRequests;

    public function mount()
    {
        $this->authorize('viewAny', TextBlock::class);

        foreach (config('shop.text-blocks') as $key) {
            TextBlock::firstOrCreate(['name' => $key], ['name' => $key, 'content' => '']);
        }
    }

    protected $title = 'Text Blocks';

    public function render()
    {
        return parent::view('livewire.backend.text-block-list-page', [
            'textBlocks' => TextBlock::query()
                ->whereIn('name', config('shop.text-blocks'))
                ->orderBy('name')
                ->get(),
        ]);
    }
}
