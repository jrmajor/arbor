<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected string $resetLink,
    ) { }

    public function build()
    {
        return $this
            ->subject(__('passwords.password_reset'))
            ->text(
                'emails.reset-password',
                ['url' => $this->resetLink],
            );
    }
}
