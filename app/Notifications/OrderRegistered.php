<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Repository\TextBlockRepository;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Illuminate\Notifications\Notification;

class OrderRegistered extends Notification
{
    private Order $order;
    private TextBlockRepository $textRepo;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->textRepo = app()->make(TextBlockRepository::class);
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
                $channels[] = TwilioChannel::class;
            }
            return $channels;
        }
        if ($notifiable instanceof Customer) {
            return [TwilioChannel::class];
        }
        return [];
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
            ->subject('Order #' . $this->order->id . ' registered by customer')
            ->markdown('mail.order.registered_by_customer', [
                'order' => $this->order,
            ]);
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage)
            ->content($this->twilioMessage($notifiable));
    }

    private function twilioMessage($notifiable): string
    {
        if ($notifiable instanceof User) {
            return sprintf("Hello %s, we have received a new order with ID #%s from customer %s.\nMore information: %s",
                $notifiable->name,
                $this->order->id,
                $this->order->customer->name,
                route('backend.orders.show', $this->order));
        }
        if ($notifiable instanceof Customer) {
            $message = __($this->textRepo->getPlain('message-order-registered'), [
                'customer_name' => $notifiable->name,
                'customer_id' => $notifiable->id_number,
                'id' => $this->order->id,
            ]);
            $message .= "\n" . __('More information: ');
            $message .= route('order-lookup', [
                'lang' => $notifiable->locale,
            ]);
            return $message;
        }
    }
}
