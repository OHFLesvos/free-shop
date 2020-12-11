<?php

namespace App\Listeners;

use App\Events\OrderSubmitted;
use App\Mail\OrderSubmitted as MailOrderSubmitted;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

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

        $this->sendMessage('Hello ' . $event->order->customer_name . ', we receiver your order with ID #' . $event->order->id . ' and will get back to you.', $event->order->customer_phone);
    }

    /**
     * Sends sms to user using Twilio's programmable sms client
     * @param String $message Body of sms
     * @param $recipients string or array of phone number of recepient
     */
    private function sendMessage($message, $recipients)
    {
        $account_sid = env("TWILIO_SID");
        $auth_token = env("TWILIO_AUTH_TOKEN");
        $twilio_number = env("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients,
                ['from' => $twilio_number, 'body' => $message] );
    }
}
