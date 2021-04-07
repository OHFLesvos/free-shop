<?php

namespace App\Http\Livewire\Backend;

use App\Models\TextBlock;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TextBlockEditPage extends BackendPage
{
    use AuthorizesRequests;

    public TextBlock $textBlock;
    public $content;

    public $locale;

    public bool $textPreview = false;

    protected function rules()
    {
        $defaultLocale = $this->defaultLocale;
        return [
            'content.*' => 'nullable',
            'content.'. $defaultLocale => config('text-blocks.' . $this->textBlock->name . '.required') ? 'required' : 'nullable',
        ];
    }

    public function mount()
    {
        $this->authorize('update', $this->textBlock);

        if (blank($this->textBlock->getTranslation('content', $this->defaultLocale))) {
            $this->locale = $this->defaultLocale;
            session()->forget('text-block-form.locale');
        } else {
            $this->locale = session()->get('text-block-form.locale', $this->defaultLocale);
        }

        $this->content = $this->textBlock->getTranslations('content');
    }

    protected function title()
    {
        return 'Edit Text Block ' . $this->textBlock->name;
    }

    public function render()
    {
        return parent::view('livewire.backend.text-block-edit-page');
    }

    public function updatedLocale($value)
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

    public function getDefaultLocaleProperty()
    {
        return config('app.fallback_locale');
    }

    public function getSupportsMarkdownProperty()
    {
        return config('text-blocks.' . $this->textBlock->name . '.type') == 'markdown';
    }
}
