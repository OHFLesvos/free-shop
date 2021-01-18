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
            'Status',
            'Customer ID',
            'Customer',
            'IP Address',
            'Browser',
            'Operating System',
            'Order',
            'Remarks',
            'Registered',
            'Updated',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->status,
            $order->customer->id,
            $order->customer->name . ', ' . $order->customer->id_number . ', ' . $this->mapPhone($order->customer->phone),
            $order->ip_address,
            $this->mapBrowser($order->user_agent),
            $this->mapOS($order->user_agent),
            $order->products
                ->sortBy('name')
                ->map(fn ($product) => sprintf('%dx %s', $product->pivot->quantity, $product->name))
                ->join(', '),
            $order->remarks,
            $this->mapDateTime($order->created_at),
            $this->mapDateTime($order->updated_at),
        ];
    }

    private function mapPhone($value)
    {
        try {
            return PhoneNumber::make($value)->formatInternational();
        } catch (Throwable $t) {
            return ' ' . $value;
        }
    }

    private function mapBrowser($value)
    {
        $ua = (new UserAgentParser())->parse($value);
        return $ua->browser() . ' ' . $ua->browserVersion();
    }

    private function mapOS($value)
    {
        $ua = (new UserAgentParser())->parse($value);
        return $ua->platform();
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
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
        ];
    }
}
