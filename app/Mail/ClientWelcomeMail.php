<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Intake;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ClientWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Intake $intake;
    public ?Order $order;
    public bool $isRenewal;

    public function __construct(User $user, Intake $intake, ?Order $order = null, bool $isRenewal = false)
    {
        $this->user      = $user;
        $this->intake    = $intake;
        $this->order     = $order;
        $this->isRenewal = $isRenewal;
    }

    public function build()
    {
        $package  = (string)($this->intake->payload['package']        ?? 'pakket_a');
        $duration = (int)   ($this->intake->payload['duration_weeks'] ?? 12);

        // Bepaal subject op basis van renewal status
        if ($this->isRenewal) {
            $subject = match ($package) {
                'pakket_a' => 'Jouw abonnement is verlengd! - Basis Pakket',
                'pakket_b' => 'Jouw abonnement is verlengd! - Chasing Goals Pakket',
                'pakket_c' => 'Jouw abonnement is verlengd! - Elite Hyrox Pakket',
                default    => 'Jouw abonnement is verlengd!',
            };
        } else {
            $subject = match ($package) {
                'pakket_a' => 'Welkom bij 2BEFIT - Basis Pakket',
                'pakket_b' => 'Welkom bij 2BEFIT - Chasing Goals Pakket',
                'pakket_c' => 'Welkom bij 2BEFIT - Elite Hyrox Pakket',
                default    => 'Welkom bij 2BEFIT',
            };
        }

        // 🔒 Hardcoded portal + acties
        $portalUrl = 'https://app.2befitlifestyle.nl';

        // Supplements shop
        $shopLink = 'https://2befitsupplements.nl/';

        // Voedingsleidraad (pdf in je project)
        // 👉 PAS DIT PAD AAN NAAR WAAR JE PDF STAAT
        // bijv. public/downloads/voedingsleidraad.pdf
        $guidelineLink = asset('downloads/voedingsleidraad.docx');

        // T-shirt formulier (tijdelijk gewoon portal)
        $shirtFormLink = 'https://app.2befitlifestyle.nl';

        $firstName = (string) Str::of($this->user->name)->before(' ');

        return $this
            ->subject($subject)
            ->view('emails.client.welcome')
            ->with([
                'user'          => $this->user,
                'intake'        => $this->intake,
                'order'         => $this->order,
                'package'       => $package,
                'duration'      => $duration,
                'portalUrl'     => $portalUrl,
                'firstName'     => $firstName,
                'shopLink'      => $shopLink,
                'guidelineLink' => $guidelineLink,
                'shirtFormLink' => $shirtFormLink,
                'isRenewal'     => $this->isRenewal,
                // 'coachChatLink' => 👈 volledig weggehaald
            ]);
    }
}
