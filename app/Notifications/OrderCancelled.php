<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Illuminate\Notifications\Notification;

class OrderCancelled extends Notification
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
            ->subject('Order #' . $this->order->id . ' cancelled by customer')
            ->markdown('mail.order.cancelled_by_customer', [
                'order' => $this->order,
            ]);
    }

    public function toTwilio($notifiable)
    {
        $message = sprintf("Hello %s, the order with ID #%d has been cancelled by customer %s.\nMore information: %s",
            $notifiable->name,
            $this->order->id,
            $this->order->customer->name,
            route('backend.orders.show', $this->order));
        return (new TwilioSmsMessage())
            ->content($message);
    }
}
