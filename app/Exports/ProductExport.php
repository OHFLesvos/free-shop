<?php

namespace App\Exports;

use App\Exports\Sheets\ProductsSheet;
use App\Services\LocalizationService;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithProperties;

class ProductExport implements WithMultipleSheets, WithProperties
{
    use Exportable;

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $localization = app()->make(LocalizationService::class);
        foreach ($localization->getLanguageCodes() as $locale) {
            $sheets[] = new ProductsSheet($locale);
        }

        return $sheets;
    }

    public function properties(): array
    {
        $appName = setting()->get('brand.name', config('app.name'));
        return [
            'title' => "$appName Products",
            'creator' => $appName,
        ];
    }
}
