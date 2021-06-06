<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRolesUpdated extends Notification
{
    use Queueable;

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('User roles have been updated')
            ->markdown('mail.user.roles_updated', [
                'name' => $notifiable->name,
                'roles' => $notifiable->getRoleNames(),
                'permissions' => $notifiable->getPermissionNames(),
            ]);
    }
}
