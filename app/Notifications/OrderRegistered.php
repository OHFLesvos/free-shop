<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Illuminate\Notifications\Notification;

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
            $channels = [];
            if ($notifiable->notify_via_email) {
                $channels[] = 'mail';
            }
            if ($notifiable->notify_via_phone && $notifiable->phone !== null) {
                if (filled(config('twilio-notification-channel.account_sid'))) {
                    $channels[] = TwilioChannel::class;
                }
            }
            if (count($channels) > 0) {
                return $channels;
            }
        }
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
            ->markdown('mail.order.registered_by_customer', [
                'order' => $this->order,
            ]);
    }

    public function toTwilio($notifiable)
    {
        $message = sprintf("Hello %s, we have received a new order with ID #%s from %s.\nMore information: %s",
            $notifiable->name,
            $this->order->id,
            $this->order->customer->name,
            route('backend.orders.show', $this->order));
        return (new TwilioSmsMessage())
            ->content($message);
    }
}
