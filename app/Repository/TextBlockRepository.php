<?php

namespace App\Repository;

use App\Models\TextBlock;
use Illuminate\Support\Str;

class TextBlockRepository
{
    public function initialize()
    {
        foreach (config('text-blocks') as $key => $item) {
            TextBlock::firstOrCreate([
                'name' => $key
            ], [
                'name' => $key,
                'content' => $item['default_content'] ?? '',
            ]);
        }
    }

    public function exists(string $name): bool
    {
        return TextBlock::whereName($name)
            ->get()
            ->filter(fn (TextBlock $textBlock) => filled($textBlock->content))
            ->isNotEmpty();
    }

    public function getMarkdown(string $name): ?string
    {
        return TextBlock::whereName($name)
            ->get()
            ->filter(fn (TextBlock $textBlock) => filled($textBlock->content))
            ->map(fn (TextBlock $textBlock) => Str::of($textBlock->content)->markdown())
            ->first()
            ?? config('text-blocks.' . $name . '.default_content');
    }

    public function getPlain(string $name, ?string $locale = null): ?string
    {
        return TextBlock::whereName($name)
            ->get()
            ->map(fn (TextBlock $textBlock) => isset($locale) ? $textBlock->getTranslation('content', $locale) : $textBlock->content)
            ->first(fn (string $content) => filled($content))
            ?? config('text-blocks.' . $name . '.default_content');
    }
}
