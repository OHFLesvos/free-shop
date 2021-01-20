<?php

namespace App\Http\Middleware;

use App\Services\CurrentCustomer;
use Closure;
use Illuminate\Http\Request;

class AuthCustomer
{
    private CurrentCustomer $currentCustomer;

    public function __construct(CurrentCustomer $currentCustomer)
    {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->currentCustomer->exists()) {
            return redirect()->route('customer.login');
        }
        return $next($request);
    }
}
