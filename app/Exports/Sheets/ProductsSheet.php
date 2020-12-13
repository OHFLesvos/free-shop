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
        return Product::orderBy('name->' . $this->locale)
            ->orderBy('name->' . config('app.fallback_locale'));
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Category',
            'Stock',
            'Free',
            'Reserved',
            'Limit per order',
            'Available',
            'Description',
            'Registered',
            'Updated',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->getTranslation('name', $this->locale),
            $product->getTranslation('category', $this->locale),
            $product->stock,
            $product->free_quantity,
            $product->reserved_quantity,
            $product->limit_per_order,
            $product->is_available ? 'Yes' : 'No',
            $product->getTranslation('description', $this->locale),
            Date::dateTimeToExcel($product->created_at->toUserTimezone()),
            Date::dateTimeToExcel($product->updated_at->toUserTimezone()),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }
}
