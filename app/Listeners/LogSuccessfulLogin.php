<?php

namespace App\Listeners;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        if ($event->user instanceof User) {
            $this->writeLogForUser($event->user);
        } elseif ($event->user instanceof Customer) {
            $this->writeLogForCustomer($event->user);
        }
    }

    private function writeLogForUser(User $user)
    {
        Log::info('Successful user login.', [
            'event.kind' => 'event',
            'event.category' => 'authentication',
            'event.type' => 'start',
            'event.outcome' => 'success',
            'user.name' => $user->name,
            'user.email' => $user->email,
            'user.roles' => $user->getRoleNames()->toArray(),
        ]);
    }

    private function writeLogForCustomer(Customer $customer)
    {
        Log::info('Successful customer login.', [
            'event.kind' => 'event',
            'event.category' => 'authentication',
            'event.type' => 'start',
            'event.outcome' => 'success',
            'customer.name' => $customer->name,
            'customer.id_number' => $customer->id_number,
            'customer.phone' => $customer->phone,
        ]);
    }
}
