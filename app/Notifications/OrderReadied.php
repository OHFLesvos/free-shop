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

class OrderReadied extends Notification
{
    use CheckBlockedPhoneNumber;

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
        return [];
    }

    public function toTwilio(Customer $notifiable): TwilioSmsMessage
    {
        return (new TwilioSmsMessage())
            ->content($this->twilioMessage($notifiable));
    }

    private function twilioMessage(Customer $notifiable): string
    {
        $message = __($this->overrideMessage ?? $this->textRepo->getPlain('message-order-ready'), [
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
        return (new MailMessage)
            ->subject(__('Your order is ready'))
            ->markdown('mail.customer.order_readied', [
                'message' => __($this->overrideMessage ?? $this->textRepo->getPlain('message-order-ready'), [
                    'customer_name' => $notifiable->name,
                    'customer_id' => $notifiable->id_number,
                    'id' => $this->order->id,
                ]),
                'url' => route('my-orders'),
            ]);
    }
}
