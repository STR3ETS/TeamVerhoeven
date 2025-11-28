<?php

namespace App\Mail;

use App\Models\Intake;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewIntakeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Intake $intake;
    public ?Order $order;

    public function __construct(User $user, Intake $intake, ?Order $order = null)
    {
        $this->user   = $user;
        $this->intake = $intake;
        $this->order  = $order;
    }

    public function build()
    {
        return $this
            ->subject('Nieuwe 2BeFit intake: ' . $this->user->name)
            ->view('emails.intakes.new_client');
    }
}
