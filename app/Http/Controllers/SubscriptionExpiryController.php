<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClientProfile;
use App\Models\Intake;
use App\Models\Order;
use App\Models\TrainingAssignment;
use App\Models\SubscriptionRenewal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class SubscriptionExpiryController extends Controller
{
    /**
     * Check of het abonnement van de client binnen 7 dagen verloopt of al verlopen is.
     * 
     * WAARSCHUWING (<=7 dagen over, niet verlopen):
     * - Informatieve popup met "Begrepen" knop
     * - Kan 1x per sessie worden getoond
     * - Geen verlengen/verwijderen opties
     * 
     * VERLOPEN (0 of minder dagen over):
     * - Popup met verlengen/verwijderen knoppen
     * - Kan NIET worden weggeklikt
     * - Wordt ALTIJD getoond (geen session check)
     * - UITZONDERING: Niet tonen als gebruiker al bezig is met verlengen (subscription_renew session)
     */
    public function check(Request $request)
    {
        $user = Auth::user();

        // Alleen clients hebben abonnementen
        if (!$user || $user->role !== 'client') {
            return response()->json(['show_popup' => false]);
        }

        // BELANGRIJK: Als de gebruiker al bezig is met verlengen, toon de popup NIET
        // Dit voorkomt dat de popup verschijnt op de intake pagina tijdens het renewal proces
        if (session('subscription_renew', false)) {
            return response()->json(['show_popup' => false]);
        }

        $profile = ClientProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            return response()->json(['show_popup' => false]);
        }

        // Haal de laatste intake op om de startdatum te bepalen
        $latestIntake = Intake::where('client_id', $user->id)
            ->whereNotNull('start_date')
            ->orderByDesc('start_date')
            ->first();

        if (!$latestIntake || !$latestIntake->start_date) {
            return response()->json(['show_popup' => false]);
        }

        // Bereken einddatum op basis van startdatum + period_weeks
        $startDate = Carbon::parse($latestIntake->start_date);
        $periodWeeks = (int) ($profile->period_weeks ?? 12);
        $endDate = $startDate->copy()->addWeeks($periodWeeks);

        // Check of we binnen 7 dagen van de einddatum zijn of al verlopen
        $now = Carbon::now();
        $daysUntilExpiryRaw = $now->diffInDays($endDate, false); // negative als al verlopen
        
        // Conservatieve afronding: floor() voor hele dagen
        $daysUntilExpiry = (int) floor($daysUntilExpiryRaw);
        $isExpired = $daysUntilExpiry < 0;

        // Bij verlopen abonnement: ALTIJD popup tonen (niet wegklikbaar, met verlengen/verwijderen)
        // Bij bijna verlopen (<=7 dagen): alleen informatieve popup met "Begrepen" knop
        if ($daysUntilExpiry <= 7 && $daysUntilExpiry >= -365) {
            // Als verlopen: negeer session check, popup altijd tonen
            // Als bijna verlopen: check session flag (kan wegklikken met "Begrepen")
            if (!$isExpired && session('subscription_popup_shown', false)) {
                return response()->json(['show_popup' => false]);
            }

            return response()->json([
                'show_popup' => true,
                'days_remaining' => (int) max(0, $daysUntilExpiry),
                'end_date' => $endDate->format('d-m-Y'),
                'package' => $latestIntake->payload['package'] ?? 'pakket_a',
                'period_weeks' => $periodWeeks,
                'is_expired' => $isExpired,
            ]);
        }

        return response()->json(['show_popup' => false]);
    }

    /**
     * Markeer dat popup is getoond (later beslissen).
     */
    public function dismiss(Request $request)
    {
        session(['subscription_popup_shown' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * Verlengen: reset intake data (behalve persoonlijke gegevens en trainer keuze),
     * annuleer oude Stripe subscription, behoud training assignments en startdatum,
     * en redirect naar intake formulier.
     * 
     * Bij renewal:
     * - period_weeks wordt OPGETELD bij bestaande period_weeks (niet vervangen)
     * - start_date blijft behouden (niet gereset)
     * - training_assignments blijven behouden (niet verwijderd)
     */
    public function renew(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'client') {
            return response()->json(['success' => false, 'message' => 'Niet ingelogd als client'], 403);
        }

        $profile = ClientProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            return response()->json(['success' => false, 'message' => 'Profiel niet gevonden'], 404);
        }

        try {
            DB::transaction(function () use ($user, $profile) {
                // 1. Annuleer Stripe subscription (aan einde van huidige periode)
                $this->cancelStripeSubscription($user->id, false);

                // 2. Training assignments worden NIET gereset bij renewal
                // De coach behoudt het bestaande trainingschema
                Log::info('[subscription.renew] training assignments preserved', ['user_id' => $user->id]);

                // 3. Behoud: user_id, coach_id, birthdate, gender, address, phone_e164, coach_preference, period_weeks
                // Reset alle andere intake-gerelateerde velden
                // BELANGRIJK: period_weeks wordt NIET gereset - dit wordt later opgeteld in CheckoutController
                $profile->height_cm = null;
                $profile->weight_kg = null;
                $profile->goals = null;
                $profile->injuries = null;
                // period_weeks wordt NIET gereset - wordt opgeteld bij renewal in CheckoutController
                $profile->frequency = null;
                $profile->background = null;
                $profile->facilities = null;
                $profile->materials = null;
                $profile->work_hours = null;
                $profile->heartrate = null;
                $profile->test_12min = null;
                $profile->test_5k = null;
                $profile->test_10k = null;
                $profile->test_marathon = null;
                $profile->goal = null;
                $profile->ftp = null;
                $profile->save();

                Log::info('[subscription.renew] profile reset (period_weeks preserved)', ['user_id' => $user->id, 'period_weeks' => $profile->period_weeks]);

                // 4. Update bestaande Intake row (niet nieuwe maken)
                // We resetten alleen de velden die opnieuw ingevuld moeten worden
                $intake = Intake::where('client_id', $user->id)
                    ->orderByDesc('id')
                    ->first();

                if ($intake) {
                    // Behoud contact gegevens (naam, email, telefoon, adres, coach voorkeur)
                    // Reset de rest van de payload
                    $existingPayload = $intake->payload ?? [];
                    $contact = $existingPayload['contact'] ?? [];
                    
                    // Behoud alleen persoonlijke gegevens en coach voorkeur
                    $newPayload = [
                        'contact' => [
                            'name' => $contact['name'] ?? null,
                            'email' => $contact['email'] ?? null,
                            'phone' => $contact['phone'] ?? null,
                            'dob' => $contact['dob'] ?? null,
                            'gender' => $contact['gender'] ?? null,
                            'street' => $contact['street'] ?? null,
                            'house_number' => $contact['house_number'] ?? null,
                            'postcode' => $contact['postcode'] ?? null,
                            'preferred_coach' => $contact['preferred_coach'] ?? null,
                            'coach_id' => $contact['coach_id'] ?? null,
                        ],
                        // Reset deze velden - worden opnieuw ingevuld
                        'package' => null,
                        'duration_weeks' => null,
                        'profile' => [],
                        'goal' => [],
                    ];

                    $intake->payload = $newPayload;
                    // BELANGRIJK: start_date wordt NIET gereset bij renewal
                    // De originele startdatum blijft behouden voor de weken-optelling berekening
                    $intake->status = 'active';
                    $intake->completed_at = null;
                    $intake->save();

                    Log::info('[subscription.renew] intake reset (start_date preserved)', ['intake_id' => $intake->id, 'start_date' => $intake->start_date]);
                }

                // 5. Registreer deze verlenging in subscription_renewals tabel
                // Dit wordt gebruikt voor het "Verlenging" label in het coach dashboard
                SubscriptionRenewal::recordRenewal($user->id);
                Log::info('[subscription.renew] renewal recorded', ['user_id' => $user->id]);
            });

            // Markeer popup als getoond
            session(['subscription_popup_shown' => true]);
            
            // Markeer dit als een renew flow zodat CheckoutController weet
            // dat bestaande intake geüpdatet moet worden
            session(['subscription_renew' => true]);

            // Redirect naar intake step 2 (pakket keuze)
            return response()->json([
                'success' => true,
                'redirect' => route('intake.index', ['step' => 2, 'renew' => 1]),
            ]);

        } catch (\Throwable $e) {
            Log::error('[subscription.renew] error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het verlengen.',
            ], 500);
        }
    }

    /**
     * Verwijder de gehele gebruiker en alle gerelateerde data.
     * Annuleer ook de Stripe subscription.
     */
    public function delete(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'client') {
            return response()->json(['success' => false, 'message' => 'Niet ingelogd als client'], 403);
        }

        $userId = $user->id;

        try {
            // 1. Annuleer Stripe subscription onmiddellijk
            $this->cancelStripeSubscription($userId, true);

            // 2. Uitloggen
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 3. Verwijder de gebruiker en alle gerelateerde data expliciet
            DB::transaction(function () use ($userId) {
                // Expliciet alle gerelateerde records verwijderen (voor het geval cascade niet werkt)
                TrainingAssignment::where('user_id', $userId)->delete();
                Intake::where('client_id', $userId)->delete();
                Order::where('client_id', $userId)->delete();
                ClientProfile::where('user_id', $userId)->delete();
                
                // Nu de user zelf verwijderen
                $userToDelete = User::find($userId);
                if ($userToDelete) {
                    $userToDelete->delete();
                    Log::info('[subscription.delete] user and all related data deleted', ['user_id' => $userId]);
                }
            });

            return response()->json([
                'success' => true,
                'redirect' => url('/'),
            ]);

        } catch (\Throwable $e) {
            Log::error('[subscription.delete] error', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het verwijderen.',
            ], 500);
        }
    }

    /**
     * Annuleer Stripe subscription voor een gebruiker.
     * 
     * @param int $userId
     * @param bool $immediately - true = onmiddellijk annuleren, false = aan einde periode
     */
    private function cancelStripeSubscription(int $userId, bool $immediately = false): void
    {
        try {
            // Zoek de laatste betaalde order voor deze user om de Stripe session ID te vinden
            $order = Order::where('client_id', $userId)
                ->where('status', 'paid')
                ->whereNotNull('provider_ref')
                ->orderByDesc('id')
                ->first();

            if (!$order || !$order->provider_ref) {
                Log::info('[stripe.cancel] no order/provider_ref found', ['user_id' => $userId]);
                return;
            }

            $stripe = new StripeClient(config('services.stripe.secret'));

            // provider_ref kan een session ID (cs_...) of access_key_... zijn
            $providerRef = $order->provider_ref;

            // Skip als het een fake/access key order is
            if (str_starts_with($providerRef, 'access_key_') || str_starts_with($providerRef, 'fake_')) {
                Log::info('[stripe.cancel] skipping fake/access_key order', [
                    'user_id' => $userId,
                    'provider_ref' => $providerRef,
                ]);
                return;
            }

            // Haal de checkout session op om de subscription ID te krijgen
            try {
                $session = $stripe->checkout->sessions->retrieve($providerRef);
                $subscriptionId = $session->subscription ?? null;

                if (!$subscriptionId) {
                    Log::info('[stripe.cancel] no subscription in session', [
                        'user_id' => $userId,
                        'session_id' => $providerRef,
                    ]);
                    return;
                }

                // Annuleer de subscription
                if ($immediately) {
                    // Onmiddellijk annuleren
                    $stripe->subscriptions->cancel($subscriptionId);
                    Log::info('[stripe.cancel] subscription canceled immediately', [
                        'user_id' => $userId,
                        'subscription_id' => $subscriptionId,
                    ]);
                } else {
                    // Annuleren aan het einde van de huidige periode
                    $stripe->subscriptions->update($subscriptionId, [
                        'cancel_at_period_end' => true,
                    ]);
                    Log::info('[stripe.cancel] subscription set to cancel at period end', [
                        'user_id' => $userId,
                        'subscription_id' => $subscriptionId,
                    ]);
                }

                // Update order status
                $order->status = 'canceled';
                $order->save();

            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Session niet gevonden of al verlopen - dit is OK
                Log::info('[stripe.cancel] session not found or expired', [
                    'user_id' => $userId,
                    'provider_ref' => $providerRef,
                    'error' => $e->getMessage(),
                ]);
            }

        } catch (\Throwable $e) {
            // Log maar gooi niet - we willen dat de rest van het process doorgaat
            Log::warning('[stripe.cancel] error', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}