<?php

namespace App\Listeners;

use App\Support\SMS\SmsSender;
use Illuminate\Support\Facades\Log;

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
            $message = __("Hello :customer_name (:customer_id), we received your order with ID #:id and will get back to you soon.\nYou ordered the following items: :items", [
                'customer_name' => $event->order->customer_name,
                'customer_id' => $event->order->customer_id_number,
                'id' => $event->order->id,
                'items' => $event->order->products->map(fn ($product) => $product->pivot->amount . 'x ' . $product->name)->join(', ')
            ]);
            $this->smsSender->sendMessage($message, $event->order->customer_phone);
        } else {
            Log::error('Unable to send SMS to customer; SMS service is not configured.', [
                'name' => $event->order->customer_name,
                'id_number' => $event->order->customer_id_number,
                'phone' => $event->order->customer_phone,
            ]);
        }
    }
}
