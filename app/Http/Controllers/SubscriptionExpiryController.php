<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClientProfile;
use App\Models\Intake;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionExpiryController extends Controller
{
    /**
     * Check of het abonnement van de client binnen 7 dagen verloopt.
     * Dit wordt gecalled bij elke pagina load voor ingelogde clients.
     */
    public function check(Request $request)
    {
        $user = Auth::user();

        // Alleen clients hebben abonnementen
        if (!$user || $user->role !== 'client') {
            return response()->json(['show_popup' => false]);
        }

        // Check of popup al getoond is deze login sessie
        if (session('subscription_popup_shown', false)) {
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

        // Check of we binnen 7 dagen van de einddatum zijn
        $now = Carbon::now();
        $daysUntilExpiry = $now->diffInDays($endDate, false); // negative als al verlopen

        if ($daysUntilExpiry <= 7 && $daysUntilExpiry >= -30) {
            // Toon popup als binnen 7 dagen van expiry (of max 30 dagen erna)
            return response()->json([
                'show_popup' => true,
                'days_remaining' => max(0, $daysUntilExpiry),
                'end_date' => $endDate->format('d-m-Y'),
                'package' => $latestIntake->payload['package'] ?? 'pakket_a',
                'period_weeks' => $periodWeeks,
                'is_expired' => $daysUntilExpiry < 0,
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
     * Verlengen: reset intake data (behalve persoonlijke gegevens en trainer keuze)
     * en redirect naar intake formulier.
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

        DB::transaction(function () use ($profile) {
            // Behoud: user_id, coach_id, birthdate, gender, address, phone_e164, coach_preference
            // Reset alle andere intake-gerelateerde velden
            $profile->height_cm = null;
            $profile->weight_kg = null;
            $profile->goals = null;
            $profile->injuries = null;
            $profile->period_weeks = 12; // Reset naar default
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
        });

        // Markeer popup als getoond
        session(['subscription_popup_shown' => true]);

        // Clear de localStorage voor de intake wizard via een flag
        session(['intake_renew' => true]);

        // Redirect naar intake step 2 (pakket keuze) - de wizard zal persoonlijke gegevens 
        // en coach voorkeur (step 0 en 1) overslaan omdat die nog gevuld zijn
        return response()->json([
            'success' => true,
            'redirect' => route('intake.index', ['step' => 2, 'renew' => 1]),
        ]);
    }

    /**
     * Verwijder de gehele gebruiker en alle gerelateerde data.
     */
    public function delete(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'client') {
            return response()->json(['success' => false, 'message' => 'Niet ingelogd als client'], 403);
        }

        // Eerst uitloggen
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Verwijder de gebruiker (cascade delete zal gerelateerde data verwijderen)
        DB::transaction(function () use ($user) {
            // De foreign keys met cascadeOnDelete zorgen ervoor dat
            // client_profiles, intakes, orders, etc. automatisch verwijderd worden
            $user->delete();
        });

        return response()->json([
            'success' => true,
            'redirect' => url('/'),
        ]);
    }
}
