<?php

namespace App\Http\Livewire\Backend;

use App\Exports\DataExport;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class DataExportPage extends Component
{
    public function render()
    {
        return view('livewire.backend.data-export-page')
            ->layout('layouts.backend', ['title' => 'Data Export']);
    }


    public $formats = [
        'xlsx' => 'Excel Spreadsheet (XLSX)',
        'ods' => 'OpenDocument Spreadsheet (ODS)',
        'csv' => 'Comma-separated values (CSV)',
        'html' => 'Web Page (HTML)',
    ];

    public $format = 'xlsx';

    public function submit()
    {
        $this->validate([
            'format' => Rule::in(array_keys($this->formats)),
        ]);

        $filename = config('app.name') . ' Data Export '. now()->toDateString() . '.' . $this->format;
        return Excel::download(new DataExport, $filename);
    }
}
