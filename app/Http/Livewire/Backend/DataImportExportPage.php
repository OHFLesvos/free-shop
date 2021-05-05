<?php

namespace App\Http\Livewire\Backend;

use App\Exports\DataExport;
use App\Exports\ReadyOrdersListExport;
use App\Imports\DataImport;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
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
        'pdf' => 'Portable Document Format (PDF)',
    ];

    public $format = 'xlsx';

    public string $type = 'complete';


    public function getTypesProperty(): array
    {
        return [
            'complete' => [
                'label' => 'Data export',
                'exportable' => new DataExport,
            ],
            'ready_orders' => [
                'label' => 'List of ready orders',
                'exportable' => new ReadyOrdersListExport,
            ],
        ];
    }

    public function export()
    {
        $this->authorize('export data');

        $this->validate([
            'format' => Rule::in(array_keys($this->formats)),
            'type' => Rule::in(array_keys($this->types)),
        ]);

        $name = $this->types[$this->type]['label'];
        $filename = config('app.name') . ' - ' . $name .' '. now()->toDateString() . '.' . $this->format;

        Log::info('Exported data to file.', [
            'event.kind' => 'event',
            'event.category' => 'database',
            'event.types' => 'info',
            'file.name' => $filename,
        ]);

        $exportable = $this->types[$this->type]['exportable'];
        return Excel::download($exportable, $filename);
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
            Product::destroy(Product::pluck('id'));
            Customer::destroy(Customer::pluck('id'));
        }

        $import = new DataImport();
        $import->import($this->upload);

        Log::info('Imported data from file.', [
            'event.kind' => 'event',
            'event.category' => 'database',
            'event.types' => 'change',
            'file.name' => $this->upload->getClientOriginalName(),
        ]);

        session()->flash('message', 'Import successful.');
    }
}
