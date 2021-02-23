<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\Order;
use App\Repository\TextBlockRepository;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class OrderReadyed extends Notification
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
        if ($notifiable instanceof Customer) {
            return [TwilioChannel::class];
        }
        return [];
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content($this->twilioMessage($notifiable));
    }

    private function twilioMessage($notifiable): string
    {
        $message = __($this->overrideMessage ?? $this->textRepo->getPlain('message-order-ready'), [
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
