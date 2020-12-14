<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductCreatePage extends Component
{
    use WithFileUploads;

    public Product $product;

    public Collection $categories;

    public $picture;

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
            'picture' => [
                'nullable',
                'image',
                'max:4096',
            ],
        ];
    }

    public string $locale;

    public $name;
    public $category;
    public $description;

    public function mount()
    {
        $this->product = new Product();
        $this->product->is_available = true;

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

            $this->locale = config('app.fallback_locale');

            $this->name = $this->product->getTranslations('name');
            $this->category = $this->product->getTranslations('category');
            $this->description = $this->product->getTranslations('description');
    }

    public function render()
    {
        return view('livewire.backend.product-form')
            ->layout('layouts.backend', ['title' => 'Register Product ']);
    }

    public function getDefaultLocaleProperty()
    {
        return config('app.fallback_locale');
    }

    public function updatedPicture()
    {
        $this->validate([
            'picture' => [
                'nullable',
                'image',
                'max:4096',
            ],
        ]);
    }

    public function submit()
    {
        $this->validate();

        $this->product->setTranslations('name', $this->name);
        $this->product->setTranslations('category', $this->category);
        $this->product->setTranslations('description', $this->description);

        if (isset($this->picture)) {
            $this->product->picture = $this->picture->storePublicly('public/pictures');
        }

        $this->product->save();

        return redirect()->route('backend.products');
    }
}
