<?php

namespace App\Exports;

use App\Exports\Sheets\OrdersSheet;
use App\Exports\Sheets\ProductsSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithProperties;

class DataExport implements WithMultipleSheets, WithProperties
{
    use Exportable;

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new OrdersSheet();
        $sheets[] = new ProductsSheet();
        return $sheets;
    }

    public function properties(): array
    {
        return [
            'title'   => config('app.name') . ' Data Export',
            'creator' => config('app.name'),
        ];
    }
}
