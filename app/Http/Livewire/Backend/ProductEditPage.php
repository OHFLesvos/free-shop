<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductEditPage extends Component
{
    use WithFileUploads;

    public Product $product;

    public $picture;

    public Collection $categories;

    public bool $removePicture = false;

    public bool $shouldDelete = false;

    protected function rules() {
        $defaultLocale = config('app.fallback_locale');
        return [
            'name.*' => 'nullable',
            'name.'. $defaultLocale => 'required',
            'category.*' => 'nullable',
            'category.' . $defaultLocale => 'required',
            'description.*' => 'nullable',
            'product.stock' => [
                'required',
                'integer',
                'min:0',
            ],
            'product.limit_per_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'product.is_available' => 'boolean',
            'picture' => 'nullable|image|max:4096',
        ];
    }

    public string $locale;

    public $name;
    public $category;
    public $description;

    public function mount()
    {
        $productCategories = Product::select('category')->get();
        $this->categories = collect(config('app.supported_languages'))
            ->keys()
            ->mapWithKeys(fn ($locale) => [$locale => $productCategories
                ->map(fn ($p) => $p->getTranslations('category'))->pluck($locale)
                ->filter()
                ->sort()
                ->unique()
                ->values()
            ]);

        $this->locale = session()->get('product-form.locale', config('app.fallback_locale'));

        $this->name = $this->product->getTranslations('name');
        $this->category = $this->product->getTranslations('category');
        $this->description = $this->product->getTranslations('description');
    }

    public function render()
    {
        return view('livewire.backend.product-form')
            ->layout('layouts.backend', ['title' => 'Edit Product ' . $this->product->name]);
    }

    public function getDefaultLocaleProperty()
    {
        return config('app.fallback_locale');
    }

    public function updatedPicture()
    {
        $this->validate([
            'picture' => 'nullable|image|max:4096',
        ]);
    }

    public function updatedLocale($value)
    {
        session()->put('product-form.locale', $value);
    }

    public function submit()
    {
        $this->validate();

        $this->product->setTranslations('name', array_map(fn ($val) => trim($val), $this->name));
        $this->product->setTranslations('category', array_map(fn ($val) => trim($val), $this->category));
        $this->product->setTranslations('description', array_map(fn ($val) => trim($val), $this->description));

        if (isset($this->product->picture) && ($this->removePicture || isset($this->picture))) {
            $this->product->picture = null;
            Storage::delete($this->product->picture);
        }

        if (isset($this->picture)) {
            $this->product->picture = $this->picture->storePublicly('public/pictures');
        }

        $this->product->save();

        return redirect()->route('backend.products');
    }

    public function delete()
    {
        if ($this->product->orders->isNotEmpty()) {
            return;
        }

        if (isset($this->product->picture)) {
            Storage::delete($this->product->picture);
        }

        $this->product->delete();

        return redirect()->route('backend.products');
    }
}
