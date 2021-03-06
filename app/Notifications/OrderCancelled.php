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

class OrderCancelled extends Notification
{
    private Order $order;
    private ?string $overrideMessage;
    private TextBlockRepository $textRepo;

    public function __construct(Order $order, ?string $overrideMessage = null)
    {
        $this->order = $order;
        $this->overrideMessage = $overrideMessage;
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
            ->subject('Order #' . $this->order->id . ' cancelled by customer')
            ->markdown('mail.order.cancelled_by_customer', [
                'order' => $this->order,
            ]);
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content($this->twilioMessage($notifiable));
    }

    private function twilioMessage($notifiable): string
    {
        if ($notifiable instanceof User) {
            return sprintf("Hello %s, the order with ID #%d has been cancelled by customer %s.\n%s",
                $notifiable->name,
                $this->order->id,
                $this->order->customer->name,
                route('backend.orders.show', $this->order));
        }
        if ($notifiable instanceof Customer) {
            $message = __($this->overrideMessage ?? $this->textRepo->getPlain('message-order-cancelled'), [
                'customer_name' => $notifiable->name,
                'customer_id' => $notifiable->id_number,
                'id' => $this->order->id,
            ]);
            $message .= "\n";
            $message .= route('my-orders');
            return $message;
        }
    }
}
