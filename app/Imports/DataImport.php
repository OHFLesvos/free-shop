<?php

namespace App\Imports;

use App\Imports\Sheets\CustomersSheet;
use App\Imports\Sheets\ProductsSheet;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataImport implements WithMultipleSheets, SkipsUnknownSheets
{
    use Importable;

    public function sheets(): array
    {
        $sheets = [];
        $defaultLocale = config('app.fallback_locale');
        $sheets['Products (' . strtoupper($defaultLocale) . ')'] = new ProductsSheet($defaultLocale);
        foreach (array_keys(config('app.supported_languages')) as $locale)
        {
            if ($defaultLocale != $locale) {
                $sheets['Products (' . strtoupper($locale) . ')'] = new ProductsSheet($locale, $defaultLocale);
            }
        }
        $sheets['Customers'] = new CustomersSheet;
        return $sheets;
    }

    public function onUnknownSheet($sheetName): void
    {
        // E.g. you can log that a sheet was not found.
        Log::info("Sheet {$sheetName} was skipped");
    }
}
