<?php

namespace App\Exports;

use App\Exports\Sheets\ProductsSheet;
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
        foreach (collect(config('localization.languages'))->pluck('code')->toArray() as $locale) {
            $sheets[] = new ProductsSheet($locale);
        }
        return $sheets;
    }

    public function properties(): array
    {
        return [
            'title'   => config('app.name') . ' Products',
            'creator' => config('app.name'),
        ];
    }
}
