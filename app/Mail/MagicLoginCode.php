<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MagicLoginCode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public int $ttlMinutes = 10
    ) {}

    public function build()
    {
        return $this->subject('Je inlogcode ('.$this->ttlMinutes.' min geldig)')
            ->text('emails.auth.magic-login-code-plain')
            ->withSymfonyMessage(function ($message) {
                // Zet Mailgun tracking uit (minder kans op "Updates/Promotions")
                $headers = $message->getHeaders();
                $headers->addTextHeader('X-Mailgun-Track', 'no');
                $headers->addTextHeader('X-Mailgun-Track-Opens', 'no');
                $headers->addTextHeader('X-Mailgun-Track-Clicks', 'no');
            });
    }
}
