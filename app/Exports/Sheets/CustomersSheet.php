<?php

namespace App\Exports\Sheets;

use App\Exports\DefaultWorksheetStyles;
use App\Models\Customer;
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

class CustomersSheet implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    use DefaultWorksheetStyles;

    protected $worksheetTitle = 'Customers';

    public function query()
    {
        return Customer::orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'ID Number',
            'Phone',
            'Language',
            'Credit',
            'Remarks',
            'Registered',
            'Updated',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->id_number,
            $this->mapPhone($customer->phone),
            strtoupper($customer->locale),
            $customer->credit,
            $customer->remarks,
            $this->mapDateTime($customer->created_at),
            $this->mapDateTime($customer->updated_at),
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

    private function mapDateTime($value)
    {
        return $value !== null
            ? Date::dateTimeToExcel($value->toUserTimezone())
            : null;
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' ' . NumberFormat::FORMAT_DATE_TIME3,
        ];
    }
}
