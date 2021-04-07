<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerIsDisabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $customer = Auth::guard('customer')->user();
        if ($customer && $customer->is_disabled) {
            Auth::guard('customer')->logout();
            $message = __('Your account has been disabled.');
            if (filled($customer->disabled_reason)) {
                $message .= ' ' . $customer->disabled_reason;
            }
            return redirect()
                ->route('customer.login')
                ->with('error', $message);
        }

        return $next($request);
    }
}
