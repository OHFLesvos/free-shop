<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegistered extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $appName = setting()->get('brand.name', config('app.name'));

        return (new MailMessage)
            ->subject('User account created in '.$appName)
            ->greeting('Hello '.$notifiable->name)
            ->line('A user account has been created for you in the application "'.$appName.'".')
            ->action('Open backend', route('backend'))
            ->line('Thank you for using our service!');
    }
}
