<?php

namespace App\Exports\Sheets;

use App\Exports\DefaultWorksheetStyles;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class OrdersSheet implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    use DefaultWorksheetStyles;

    protected $worksheetTitle = 'Orders';

    protected array $columnAlignment = [
        // 'C' => Alignment::HORIZONTAL_RIGHT,
        // 'E' => Alignment::HORIZONTAL_RIGHT,
    ];

    public function query()
    {
        return Order::orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'Registered',
            'Customer Name',
            'Customer ID Number',
            'Customer Phone',
            'Customer IP Address',
            'Customer Browser',
            'Customer Platform',
            'Customer Language',
            'Order',
            'Remarks',
            'Updated',
            'Completed',
            'Cancelled',
        ];
    }

    public function map($order): array
    {
        try {
            $phone = PhoneNumber::make($order->customer_phone)->formatInternational();
        } catch (Throwable $t) {
            $phone = ' ' . $order->customer_phone;
        }
        return [
            Date::dateTimeToExcel($order->created_at->toUserTimezone()),
            $order->customer_name,
            $order->customer_id_number,
            $phone,
            $order->customer_ip_address,
            $order->UA->browser() . ' ' . $order->UA->browserVersion(),
            $order->UA->platform(),
            $order->locale,
            $order->products
                ->sortBy('name')
                ->map(fn ($product) => sprintf('%dx %s', $product->pivot->quantity, $product->name))
                ->join(', '),
            $order->remarks,
            Date::dateTimeToExcel($order->updated_at->toUserTimezone()),
            $order->completed_at !== null
                ? Date::dateTimeToExcel($order->completed_at->toUserTimezone())
                : null,
            $order->cancelled_at !== null
                ? Date::dateTimeToExcel($order->cancelled_at->toUserTimezone())
                : null,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
        ];
    }
}
