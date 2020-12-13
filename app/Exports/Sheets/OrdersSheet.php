<?php

namespace App\Exports\Sheets;

use donatj\UserAgent\UserAgentParser;
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

    public function query()
    {
        return Order::orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Customer ID Number',
            'Customer Phone',
            'Customer IP Address',
            'Customer Browser',
            'Customer Platform',
            'Customer Language',
            'Order',
            'Remarks',
            'Registered',
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
        $ua = (new UserAgentParser())->parse($order->customer_user_agent);
        return [
            $order->id,
            $order->customer_name,
            $order->customer_id_number,
            $phone,
            $order->customer_ip_address,
            $ua->browser() . ' ' . $ua->browserVersion(),
            $ua->platform(),
            strtoupper($order->locale),
            $order->products
                ->sortBy('name')
                ->map(fn ($product) => sprintf('%dx %s', $product->pivot->quantity, $product->name))
                ->join(', '),
            $order->remarks,
            Date::dateTimeToExcel($order->created_at->toUserTimezone()),
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
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
            'L' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
            'M' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
            'N' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
        ];
    }
}
