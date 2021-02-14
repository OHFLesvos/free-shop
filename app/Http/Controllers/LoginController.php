<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('backend.login');
    }

    public function logout()
    {
        return redirect()
            ->route('backend.login')
            ->with(Auth::logout());
    }
}
