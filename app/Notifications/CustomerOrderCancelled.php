<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class CustomerOrderCancelled extends Notification
{
    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable instanceof Customer) {
            if (filled(config('twilio-notification-channel.account_sid'))) {
                return [TwilioChannel::class];
            }
            Log::warning('Cannot send notification, SMS provider not properly configured.');
        }
    }

    public function toTwilio($notifiable)
    {
        $message = __("Hello :customer_name (ID :customer_id). Your order with ID #:id has been cancelled.", [
            'customer_name' => $notifiable->name,
            'customer_id' => $notifiable->id_number,
            'id' => $this->order->id,
        ]);
        $message .= "\n" . __('More information: ');
        $message .= route('order-lookup', [
            'id_number' => $notifiable->id_number,
            'phone' => $notifiable->phone,
            'lang' => $notifiable->locale,
        ]);
        return (new TwilioSmsMessage())
            ->content($message);
    }
}
