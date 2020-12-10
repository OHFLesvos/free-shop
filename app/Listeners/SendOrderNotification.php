<?php

namespace App\Listeners;

use App\Events\OrderSubmitted;
use App\Mail\OrderSubmitted as MailOrderSubmitted;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendOrderNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderSubmitted  $event
     * @return void
     */
    public function handle(OrderSubmitted $event)
    {
        // Send mail to admins
        User::all()->each(function ($user) use ($event) {
            Mail::to($user)->send(new MailOrderSubmitted($event->order));
        });
    }
}
