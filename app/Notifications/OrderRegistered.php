<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderRegistered extends Notification
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
        if ($notifiable instanceof User) {
            return ['mail'];
        }
        if ($notifiable instanceof Order) {
            if (filled(config('twilio-notification-channel.account_sid'))) {
                return [TwilioChannel::class];
            }
            Log::warning('Cannot send notification, SMS provider not properly configured.');
        }
    }

    public function toTwilio($notifiable)
    {
        $message = __("Hello :customer_name (ID :customer_id), we have received your order with ID #:id and will get back to you soon.", [
            'customer_name' => $this->order->customer_name,
            'customer_id' => $this->order->customer_id_number,
            'id' => $this->order->id,
        ]);
        $message .= "\n" . __('More information: ') . route('order-lookup', ['id_number' => $this->order->customer_id_number, 'phone' => $this->order->customer_phone]);
        return (new TwilioSmsMessage())
            ->content($message);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order #' . $this->order->id . ' registered')
            ->markdown('mail.order.registered', [
                'order' => $this->order,
            ]);
    }
}
