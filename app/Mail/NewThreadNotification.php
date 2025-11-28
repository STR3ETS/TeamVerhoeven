<?php

namespace App\Mail;

use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewThreadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Thread $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    public function build()
    {
        return $this
            ->subject('Nieuwe chat van ' . ($this->thread->clientUser->name ?? 'Onbekende klant'))
            ->view('emails.threads.new_thread_for_coach');
    }
}
