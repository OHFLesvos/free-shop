<?php

namespace App\Facades;

use App\Services\CurrentCustomer as CurrentCustomerService;
use Illuminate\Support\Facades\Facade;

class CurrentCustomer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CurrentCustomerService::class;
    }
}
