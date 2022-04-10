<?php

namespace App\Http\Livewire\Backend;

use App\Models\Product;
use App\Services\LocalizationService;
use Gumlet\ImageResize;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\WithFileUploads;

class ProductManagePage extends BackendPage
{
    use AuthorizesRequests;
    use WithFileUploads;

    public Product $product;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $picture;

    public Collection $categories;

    public bool $removePicture = false;

    public string $locale;

    public array $name;

    public array $category;

    public array $description;

    protected function rules(): array
    {
        $defaultLocale = config('app.fallback_locale');
        return [
            'name.*' => 'nullable',
            'name.' . $defaultLocale => 'required',
            'category.*' => 'nullable',
            'category.' . $defaultLocale => 'required',
            'description.*' => 'nullable',
            'product.sequence' => [
                'required',
                'integer',
                'min:0',
            ],
            'product.price' => [
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

    public function mount(LocalizationService $localization): void
    {
        if (isset($this->product)) {
            $this->authorize('update', $this->product);
        } else {
            $this->authorize('create', Product::class);
        }

        if (!isset($this->product)) {
            $this->product = new Product();
            $this->product->is_available = true;
            $this->product->sequence = Product::count();
        }

        $productCategories = Product::select('category')->get();
        $this->categories = collect($localization->getLanguageCodes())
            ->mapWithKeys(fn ($locale) => [
                $locale => $productCategories
                    ->map(fn ($p) => $p->getTranslations('category'))->pluck($locale)
                    ->filter()
                    ->sort()
                    ->unique()
                    ->values()
            ]);

        if ($this->product->exists) {
            $this->locale = session()->get('product-form.locale', config('app.fallback_locale'));
        } else {
            $this->locale = config('app.fallback_locale');
        }

        $this->name = $this->product->getTranslations('name');
        $this->category = $this->product->getTranslations('category');
        $this->description = $this->product->getTranslations('description');
    }

    protected function title(): string
    {
        return $this->product->exists
            ? 'Edit Product ' . $this->product->name
            : 'Register Product';
    }

    public function render(): View
    {
        return parent::view('livewire.backend.product-form', [
            'title' => $this->product->exists ? 'Edit Product' : 'Register Product',
        ]);
    }

    public function getDefaultLocaleProperty(): string
    {
        return config('app.fallback_locale');
    }

    public function updatedPicture(): void
    {
        $this->validate([
            'picture' => [
                'nullable',
                'image',
                'max:4096',
            ],
        ]);
    }

    public function updatedLocale(string $value): void
    {
        session()->put('product-form.locale', $value);
    }

    public function submit()
    {
        $this->authorize('update', $this->product);

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

            $image = new ImageResize(Storage::path($this->product->picture));
            $image->resizeToWidth(config('shop.product.max_picture_width'));
            $image->save(Storage::path($this->product->picture));
        }

        $this->product->save();

        session()->flash('message', $this->product->wasRecentlyCreated
            ? 'Product registered.'
            : 'Product updated.');

        return redirect()->route('backend.products');
    }

    public function delete()
    {
        $this->authorize('delete', $this->product);

        if (isset($this->product->picture)) {
            Storage::delete($this->product->picture);
        }

        $this->product->delete();

        session()->flash('message', 'Product deleted.');

        return redirect()->route('backend.products');
    }
}
