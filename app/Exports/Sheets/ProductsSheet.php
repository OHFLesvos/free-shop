<?php

namespace App\Exports\Sheets;

use App\Exports\DefaultWorksheetStyles;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductsSheet implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    use DefaultWorksheetStyles;

    protected $worksheetTitle = 'Products';

    protected array $columnAlignment = [
        'C' => Alignment::HORIZONTAL_RIGHT,
        'D' => Alignment::HORIZONTAL_RIGHT,
    ];

    private string $locale;

    public function __construct($locale)
    {
        $this->locale = $locale;
        $this->worksheetTitle .= ' (' . strtoupper($locale) . ')';
    }

    public function query()
    {
        return Product::orderBy('name->' . $this->locale);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Category',
            'Stock',
            'Limit per order',
            'Available',
            'Description',
            'Registered',
            'Updated',
        ];
    }

    public function map($order): array
    {
        return [
            $order->getTranslation('name', $this->locale),
            $order->getTranslation('category', $this->locale),
            $order->stock,
            $order->limit_per_order,
            $order->is_available ? 'Yes' : 'No',
            $order->getTranslation('description', $this->locale),
            Date::dateTimeToExcel($order->created_at->toUserTimezone()),
            Date::dateTimeToExcel($order->updated_at->toUserTimezone()),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }
}
