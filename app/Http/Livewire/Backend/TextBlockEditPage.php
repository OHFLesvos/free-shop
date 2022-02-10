<?php

namespace App\Http\Livewire\Backend;

use App\Models\TextBlock;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class TextBlockEditPage extends BackendPage
{
    use AuthorizesRequests;

    public TextBlock $textBlock;
    public array $content;
    public string $locale;
    public bool $textPreview = false;

    protected function rules(): array
    {
        $defaultLocale = $this->getDefaultLocaleProperty();
        return [
            'content.*' => 'nullable',
            'content.' . $defaultLocale => config('text-blocks.' . $this->textBlock->name . '.required') ? 'required' : 'nullable',
        ];
    }

    public function mount(): void
    {
        $this->authorize('update', $this->textBlock);

        $defaultLocale = $this->getDefaultLocaleProperty();
        if (blank($this->textBlock->getTranslation('content', $defaultLocale))) {
            $this->locale = $defaultLocale;
            session()->forget('text-block-form.locale');
        } else {
            $this->locale = session()->get('text-block-form.locale', $defaultLocale);
        }

        $this->content = $this->textBlock->getTranslations('content');
    }

    protected function title(): string
    {
        return 'Edit Text Block ' . $this->textBlock->name;
    }

    public function render(): View
    {
        return parent::view('livewire.backend.text-block-edit-page');
    }

    public function updatedLocale(string $value): void
    {
        session()->put('text-block-form.locale', $value);
    }

    public function submit()
    {
        $this->authorize('update', $this->textBlock);

        $this->validate();

        $this->textBlock->setTranslations('content', array_map(fn ($val) => trim($val), $this->content));
        $this->textBlock->save();

        session()->flash('message', 'Text block updated.');

        return redirect()->route('backend.configuration.text-blocks');
    }

    public function getDefaultLocaleProperty(): string
    {
        return config('app.fallback_locale');
    }

    public function getSupportsMarkdownProperty(): bool
    {
        return config('text-blocks.' . $this->textBlock->name . '.type') == 'markdown';
    }
}
