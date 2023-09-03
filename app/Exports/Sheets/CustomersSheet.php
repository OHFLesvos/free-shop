<?php

namespace App\Exports\Sheets;

use App\Exports\DefaultWorksheetStyles;
use App\Models\Customer;
use App\Models\Tag;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Throwable;

class CustomersSheet implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStyles
{
    use DefaultWorksheetStyles;

    protected $worksheetTitle = 'Customers';

    protected $tags;

    public function __construct()
    {
        $this->tags = Tag::orderBy('name')
            ->has('customers')
            ->pluck('name');
    }

    public function query()
    {
        return Customer::orderBy('name')
            ->with('tags');
    }

    public function headings(): array
    {
        $data = [
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

        foreach ($this->tags as $tag) {
            $data[] = $tag;
        }

        return $data;
    }

    public function map($customer): array
    {
        $tags = $customer->tags->sortBy('name')->pluck('name');
        $data = [
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

        foreach ($this->tags as $tag) {
            $data[] = $tags->contains($tag) ? 'X' : null;
        }

        return $data;
    }

    private function mapPhone($value)
    {
        try {
            return phone($value)->formatInternational();
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
