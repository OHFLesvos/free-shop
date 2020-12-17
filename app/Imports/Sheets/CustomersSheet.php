<?php

namespace App\Imports\Sheets;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomersSheet implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithEvents
{
    use SkipsErrors;

    private $countImports = 0;

    public function model(array $row)
    {
        ++$this->countImports;

        $customer = Customer::find($row['id']);
        if ($customer == null) {
            $customer = new Customer();
            $customer->id = $row['id'];
        }
        $customer->fill([
            'name' => $row['name'],
            'id_number' => $row['id_number'],
            'credit' => $row['credit'],
            'phone' => $row['phone'],
            'locale' => $row['language'],
            'remarks' => $row['remarks'],
        ]);
        return $customer;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet ::class => function(AfterSheet $event) {
                info("Imported {$this->countImports} customers.");
            },
        ];
    }
}
