<?php

namespace App\Http\Livewire\Backend;

use App\Exports\DataExport;
use App\Imports\DataImport;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class DataImportExportPage extends BackendPage
{
    use WithFileUploads;
    use AuthorizesRequests;

    public $upload;

    public bool $delete_existing_data = false;

    protected $rules = [
        'delete_existing_data' => 'boolean',
    ];

    protected $title = 'Data Import & Export';

    public function render()
    {
        return parent::view('livewire.backend.data-import-export-page');
    }

    public $formats = [
        'xlsx' => 'Excel Spreadsheet (XLSX)',
        'ods' => 'OpenDocument Spreadsheet (ODS)',
        'csv' => 'Comma-separated values (CSV)',
        'html' => 'Web Page (HTML)',
    ];

    public $format = 'xlsx';

    public function export()
    {
        $this->authorize('export data');

        $this->validate([
            'format' => Rule::in(array_keys($this->formats)),
        ]);

        $filename = config('app.name') . ' Data Export '. now()->toDateString() . '.' . $this->format;
        return Excel::download(new DataExport, $filename);
    }

    public function import()
    {
        $this->authorize('import data');

        $this->validate([
            'upload' => [
                'file',
                'max:1024',
            ],
        ]);

        if ($this->delete_existing_data) {
            Product::destroy(Product::all()->pluck('id'));
            Customer::destroy(Customer::all()->pluck('id'));
        }

        $import = new DataImport();
        $import->import($this->upload);

        session()->flash('message', 'Import successful.');
    }
}
