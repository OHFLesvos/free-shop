<?php

namespace App\Notifications;

use App\Support\CheckBlockedPhoneNumber;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class OtpRequired extends Notification
{
    use CheckBlockedPhoneNumber;
    
    private $code;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $this->checkBlockedPhoneNumber($notifiable->phone);
        return [TwilioChannel::class];
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content(__('Your code is: :code', ['code' => $this->code]));
    }
}
