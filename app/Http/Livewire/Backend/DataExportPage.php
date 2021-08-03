<?php

namespace App\Http\Livewire\Backend;

use App\Exports\DataExport;
use App\Exports\ReadyOrdersListExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class DataExportPage extends BackendPage
{
    use WithFileUploads;
    use AuthorizesRequests;

    protected string $title = 'Data Export';

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $upload;

    public bool $deleteExistingData = false;

    protected array $rules = [
        'deleteExistingData' => 'boolean',
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

    public function render(): View
    {
        return parent::view('livewire.backend.data-export-page');
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
}
