<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Intake;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ClientRenewalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $client;
    public User $coach;
    public Intake $intake;
    public ?Order $order;
    public int $addedWeeks;
    public int $totalWeeks;
    public ?string $endDate;

    public function __construct(User $client, User $coach, Intake $intake, ?Order $order = null)
    {
        $this->client = $client;
        $this->coach  = $coach;
        $this->intake = $intake;
        $this->order  = $order;

        // Bereken toegevoegde weken en totaal
        $this->addedWeeks = (int)($intake->payload['duration_weeks'] ?? 12);
         $this->totalWeeks = (int)($client->clientProfile?->period_weeks ?? $this->addedWeeks);

        // Bereken einddatum
        $startDate = $intake->start_date;
        if ($startDate) {
            $this->endDate = $startDate->copy()->addWeeks($this->totalWeeks)->format('d-m-Y');
        } else {
            $this->endDate = null;
        }
    }

    public function build()
    {
        $clientName = $this->client->name ?? 'Onbekende klant';
        
        $package = match ($this->intake->payload['package'] ?? 'pakket_a') {
            'pakket_a' => 'Basis Pakket',
            'pakket_b' => 'Chasing Goals Pakket',
            'pakket_c' => 'Elite Hyrox Pakket',
            default    => 'Onbekend pakket',
        };

        return $this
            ->subject("🔄 {$clientName} heeft abonnement verlengd")
            ->view('emails.client-renewal-notification')
            ->with([
                'client'      => $this->client,
                'coach'       => $this->coach,
                'intake'      => $this->intake,
                'order'       => $this->order,
                'package'     => $package,
                'addedWeeks'  => $this->addedWeeks,
                'totalWeeks'  => $this->totalWeeks,
                'endDate'     => $this->endDate,
                'clientName'  => $clientName,
                'coachName'   => Str::of($this->coach->name)->before(' '),
                'portalUrl'   => 'https://app.2befitlifestyle.nl',
            ]);
    }
}
