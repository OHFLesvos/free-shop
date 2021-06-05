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
use Symfony\Component\HttpFoundation\Response;

class DataImportExportPage extends BackendPage
{
    use WithFileUploads;
    use AuthorizesRequests;

    protected string $title = 'Data Import & Export';

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $upload;

    public bool $delete_existing_data = false;

    protected array $rules = [
        'delete_existing_data' => 'boolean',
    ];

    public array $formats = [
        'xlsx' => 'Excel Spreadsheet (XLSX)',
        'ods' => 'OpenDocument Spreadsheet (ODS)',
        'csv' => 'Comma-separated values (CSV)',
        'html' => 'Web Page (HTML)',
        'pdf' => 'Portable Document Format (PDF)',
    ];

    public string $format = 'xlsx';
    public string $type = 'complete';

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return parent::view('livewire.backend.data-import-export-page');
    }

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

    public function export(): Response
    {
        $this->authorize('export data');

        $types = $this->getTypesProperty();

        $this->validate([
            'format' => Rule::in(array_keys($this->formats)),
            'type' => Rule::in(array_keys($types)),
        ]);

        $name = $types[$this->type]['label'];
        $filename = config('app.name') . ' - ' . $name .' '. now()->toDateString() . '.' . $this->format;

        Log::info('Exported data to file.', [
            'event.kind' => 'event',
            'event.category' => 'database',
            'event.types' => 'info',
            'file.name' => $filename,
        ]);

        $exportable = $types[$this->type]['exportable'];

        return Excel::download($exportable, $filename);
    }

    public function import(): void
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
