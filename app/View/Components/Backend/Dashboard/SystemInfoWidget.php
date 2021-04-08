<?php

namespace App\View\Components\Backend\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use PDO;

class SystemInfoWidget extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if (Auth::user()->can('view system information')) {
            $data = [
                'OS' => PHP_OS_FAMILY,
                'Web server' => $_SERVER['SERVER_SOFTWARE'],
                'PHP version' => phpversion(),
                'Database' => sprintf("%s (%s)", DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME), DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION))
            ];
            $gitInfoFile = base_path('.gitinfo');
            if (file_exists($gitInfoFile)) {
                $data['Software version'] = file_get_contents($gitInfoFile);
            }
            return view('components.backend.dashboard.system-info-widget', [
                'data' => $data,
            ]);
        }
        return null;
    }
}
