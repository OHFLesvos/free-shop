<?php

namespace App\Listeners;

use App\Support\SMS\SmsSender;

class SendOrderNotificationToCustomer
{
    private SmsSender $smsSender;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SmsSender $smsSender)
    {
        $this->smsSender = $smsSender;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($this->smsSender->isConfigured())
        {
            $message = sprintf("Hello %s (%s), we received your order with ID #%d and will get back to you soon.\nYou ordered: %s",
                $event->order->customer_name,
                $event->order->customer_id_number,
                $event->order->id,
                $event->order->products->map(fn ($product) => $product->pivot->amount . 'x ' . $product->name)->join(', ')
            );
            $this->smsSender->sendMessage($message, $event->order->customer_phone);
        }
    }
}
