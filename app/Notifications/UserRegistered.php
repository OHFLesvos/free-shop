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
        return (new MailMessage)
            ->subject('User account created in ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name)
            ->line('A user account has been created for you in the application "' . config('app.name') . '".')
            ->action('Open backend', route('backend'))
            ->line('Thank you for using our service!');
    }
}
