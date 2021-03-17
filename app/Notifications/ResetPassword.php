<?php

namespace App\Notifications;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Mail\Mailable;
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

    public function toMail(User $notifiable): Mailable
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new ResetPasswordMail($url))
            ->to($notifiable->email);
    }
}
