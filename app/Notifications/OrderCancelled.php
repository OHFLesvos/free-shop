<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\Order;
use App\Notifications\Traits\CheckBlockedPhoneNumber;
use App\Repository\TextBlockRepository;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class OrderCancelled extends Notification
{
    use CheckBlockedPhoneNumber;

    private TextBlockRepository $textRepo;

    public function __construct(
        private Order $order,
        private ?string $overrideMessage = null
    ) {
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
        $channels = [];
        if ($notifiable instanceof Customer) {
            if ($notifiable->phone !== null) {
                $this->checkBlockedPhoneNumber($notifiable->phone);
                $channels[] = TwilioChannel::class;
            }
            if ($notifiable->email !== null) {
                $channels[] = 'mail';
            }
        }
        return $channels;
    }

    public function toTwilio(Customer $notifiable): TwilioSmsMessage
    {
        return (new TwilioSmsMessage())
            ->content($this->twilioMessage($notifiable));
    }

    private function twilioMessage(Customer $notifiable): string
    {
        $message = __($this->overrideMessage ?? $this->textRepo->getPlain('message-order-cancelled'), [
            'customer_name' => $notifiable->name,
            'customer_id' => $notifiable->id_number,
            'id' => $this->order->id,
        ]);
        $message .= "\n";
        $message .= route('my-orders');
        return $message;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Your order has been cancelled'))
            ->markdown('mail.customer.order_status changed', [
                'title' => __('Your order has been cancelled'),
                'message' => __($this->overrideMessage ?? $this->textRepo->getPlain('message-order-cancelled'), [
                    'customer_name' => $notifiable->name,
                    'customer_id' => $notifiable->id_number,
                    'id' => $this->order->id,
                ]),
                'url' => route('my-orders'),
                'products' => $this->order->products->sortBy('name'),
            ]);
    }
}
