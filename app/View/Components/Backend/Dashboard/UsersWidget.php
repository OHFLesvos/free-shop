<?php

namespace App\View\Components\Backend\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class UsersWidget extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if (Auth::user()->can('viewAny', App\Models\User::class)) {
            return view('components.backend.dashboard.users-widget', [
                'registeredUsers' => User::count(),
            ]);
        }
        return null;
    }
}
