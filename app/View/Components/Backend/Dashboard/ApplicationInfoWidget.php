<?php

namespace App\View\Components\Backend\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class ApplicationInfoWidget extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if (Auth::user()->can('view system information')) {
            $data = [];
            $gitInfoFile = base_path('.gitinfo');
            if (file_exists($gitInfoFile)) {
                $content = file_get_contents($gitInfoFile);
                $lines = array_filter(array_map('trim', explode("\n", $content)));
                foreach ($lines as $line) {
                    $parts = explode(': ', $line, 2);
                    if (count($parts) == 2) {
                        $data[$parts[0]] = $parts[1];
                    }
                }
            }
            $data['Laravel'] = app()->version();

            return view('components.backend.dashboard.key-value-widget-template', [
                'title' => 'Software application',
                'data' => $data,
            ]);
        }

        return '';
    }
}
