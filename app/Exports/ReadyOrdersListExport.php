<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ReadyOrdersListExport implements WithProperties, FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    use Exportable;
    use DefaultWorksheetStyles;

    protected $worksheetTitle = 'Orders';

    protected $orientation = PageSetup::ORIENTATION_PORTRAIT;

    public function query()
    {
        return Order::status('ready')
            ->with('customer')
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer Name',
            'Customer ID Number',
            'Registered',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            isset($order->customer) ? ($order->customer->name) : null,
            isset($order->customer) ? ($order->customer->id_number) : null,
            $this->mapDateTime($order->created_at),
        ];
    }

    private function mapDateTime($value)
    {
        return $value !== null
            ? Date::dateTimeToExcel($value->toUserTimezone())
            : null;
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_YYYYMMDD.' '.NumberFormat::FORMAT_DATE_TIME3,
        ];
    }

    public function properties(): array
    {
        return [
            'title' => config('app.name').' List of ready orders',
            'creator' => config('app.name'),
        ];
    }
}
