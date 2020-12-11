<?php

namespace App\Listeners;

use App\Events\OrderSubmitted;
use App\Mail\OrderSubmitted as MailOrderSubmitted;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendOrderNotificationToStaff
{
    /**
     * Handle the event.
     *
     * @param  OrderSubmitted  $event
     * @return void
     */
    public function handle(OrderSubmitted $event)
    {
        User::all()->each(function ($user) use ($event) {
            Mail::to($user)->send(new MailOrderSubmitted($event->order));
        });
    }
}
