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

        $message = 'Hello ' . $event->order->customer_name . ' (' . $event->order->customer_id_number . ')' .
            ', we received your order with ID #' . $event->order->id . ' and will get back to you. You ordered: '.
            $event->order->products->map(fn ($product) => $product->pivot->amount . 'x ' . $product->name)->join(', ');
        $this->sendMessage($message, $event->order->customer_phone);
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
