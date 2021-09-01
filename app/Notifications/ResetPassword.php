<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
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

        return new class ($notifiable->email, $url) extends Mailable {
            use Queueable;

            public function __construct(
                string $to,
                protected string $url,
            ) {
                $this->to($to);
            }

            public function build()
            {
                return $this
                    ->subject(__('passwords.password_reset'))
                    ->text(
                        'emails.reset-password',
                        ['url' => $this->url],
                    );
            }
        };
    }
}
