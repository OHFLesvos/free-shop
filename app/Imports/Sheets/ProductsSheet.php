<?php

namespace App\Imports\Sheets;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductsSheet implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithEvents
{
    use SkipsErrors;

    private string $locale;
    private ?string $defaultLocale;

    private $countImports = 0;

    public function __construct(string $locale, ?string $defaultLocale = null)
    {
        $this->locale = $locale;
        $this->defaultLocale = $defaultLocale;
    }

    public function model(array $row)
    {
        ++$this->countImports;

        $product = Product::find($row['id']);
        if ($product == null) {
            $product = new Product([
                'price' => $row['price'],
                'stock' => $row['stock'],
                'limit_per_order' => $row['limit_per_order'],
                'is_available' => in_array($row['available'], ['Yes', 1, 'true']),
            ]);
            $product->id = $row['id'];
        }
        if ($this->defaultLocale === null || $product->getTranslation('name', $this->defaultLocale) != $row['name']) {
            $product->setTranslation('name', $this->locale, $row['name']);
        }
        if ($this->defaultLocale === null || $product->getTranslation('category', $this->defaultLocale) != $row['category']) {
            $product->setTranslation('category', $this->locale, $row['category']);
        }
        if ($this->defaultLocale === null || $product->getTranslation('description', $this->defaultLocale) != $row['description']) {
            $product->setTranslation('description', $this->locale, $row['description']);
        }
        return $product;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'category' => 'required',
            'description' => 'nullable',
            'stock' => [
                'required',
                'integer',
                'min:0',
            ],
            'limit_per_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet ::class => function(AfterSheet $event) {
                info("Imported {$this->countImports} products.");
            },
        ];
    }
}
