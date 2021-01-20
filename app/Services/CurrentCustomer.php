<?php

namespace App\Services;

use App\Models\Customer;

class CurrentCustomer
{
    private ?Customer $customer = null;

    public function __construct()
    {
        if (session()->has('customer')) {
            $this->customer = Customer::find(session()->get('customer'));
            if ($this->customer == null) {
                session()->forget('customer');
            }
        }
    }

    public function exists(): bool
    {
        return $this->customer !== null;
    }

    public function get(): ?Customer
    {
        return $this->customer;
    }

    public function set(Customer $customer)
    {
        session()->put('customer', $customer->id);
        $this->customer = $customer;
    }

    public function forget()
    {
        session()->forget('customer');
        $this->customer = null;
    }
}
