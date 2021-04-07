<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\Order;
use App\Notifications\Traits\CheckBlockedPhoneNumber;
use App\Repository\TextBlockRepository;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class OrderRegistered extends Notification
{
    use CheckBlockedPhoneNumber;

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
        if ($notifiable instanceof Customer) {
            $this->checkBlockedPhoneNumber($notifiable->phone);
            return [TwilioChannel::class];
        }
        return [];
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage)
            ->content($this->twilioMessage($notifiable));
    }

    private function twilioMessage(Customer $notifiable): string
    {
        $message = __($this->textRepo->getPlain('message-order-registered'), [
            'customer_name' => $notifiable->name,
            'customer_id' => $notifiable->id_number,
            'id' => $this->order->id,
        ]);
        $message .= "\n";
        $message .= route('my-orders');
        return $message;
    }
}
