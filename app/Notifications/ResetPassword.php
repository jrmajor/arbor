<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    public function __construct(
        public string $token,
    ) { }

    public function via(): string
    {
        return 'mail';
    }

    public function toMail(User $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage())
            ->subject(__('passwords.reset_password'))
            ->line(__('passwords.you_are_receiving'))
            ->action(__('passwords.reset_password'), $url)
            ->line(__('passwords.reset_link_will_expire', [
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]))
            ->line(__('passwords.if_you_didnt_request'));
    }
}
