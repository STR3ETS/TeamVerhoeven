<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClientProfile;
use App\Services\UhvCalculator;
use Illuminate\Http\Request;

class CoachClientController extends Controller
{
    /**
     * Lijst met cliënten (eenvoudig filter op naam/email).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $clients = User::query()
            ->where('role', 'client')
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->with(['clientProfile'])
            ->paginate(20);

        return view('coach.clients.index', compact('clients', 'q'));
    }

    /**
     * Detail van één cliënt met UHV-berekening.
     */
    public function show(User $client, Request $request)
    {
        // Alleen coaches mogen dit zien; route heeft al 'role:coach' middleware.
        $coach = $request->user();

        // Haal profiel op
        $profile = ClientProfile::where('user_id', $client->id)->first();

        // Optioneel: autorisatie op coach-koppeling
        if ($profile && $profile->coach_id && (int) $profile->coach_id !== (int) $coach->id) {
            abort(403);
        }

        // Leeftijd uit birthdate
        $birthdate = $profile?->birthdate; // cast naar Carbon via $casts
        $ageYears  = $birthdate ? $birthdate->age : null;

        // Inputs uit onboarding
        $distance12min = null;
        if ($profile && is_array($profile->test_12min)) {
            $distance12min = (int) ($profile->test_12min['meters'] ?? 0);
        }

        $hrRest = null;
        $hrMax  = null;
        if ($profile && is_array($profile->heartrate)) {
            $hrRest = isset($profile->heartrate['resting']) ? (int) $profile->heartrate['resting'] : null;
            $hrMax  = isset($profile->heartrate['max'])      ? (int) $profile->heartrate['max']      : null;
        }

        // UHV berekenen als alle drie aanwezig zijn
        $uhvData = null;
        if ($distance12min && $hrRest && $hrMax) {
            $calc    = UhvCalculator::make($distance12min, $hrRest, $hrMax);
            $uhvData = $calc->toArray();
        }

        return view('coach.clients.show', compact(
            'client',
            'profile',
            'birthdate',
            'ageYears',
            'distance12min',
            'hrRest',
            'hrMax',
            'uhvData'
        ));
    }

    public function intake(User $client, Request $request)
    {
        $coach   = $request->user();
        $profile = ClientProfile::where('user_id', $client->id)->first();

        if ($profile && $profile->coach_id && (int) $profile->coach_id !== (int) $coach->id) {
            abort(403);
        }

        // Basisgegevens
        $birthdate = $profile?->birthdate; // Carbon (via $casts)
        $ageYears  = $birthdate ? $birthdate->age : null;

        // JSON-achtige velden robuust parsen
        $parseJson = function ($value, $fallback = []) {
            if (is_array($value)) return $value;
            if (is_string($value) && trim($value) !== '') {
                $dec = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($dec)) {
                    return $dec;
                }
            }
            return $fallback;
        };

        $address     = $parseJson($profile?->address, []);
        $heartrate   = $parseJson($profile?->heartrate, []);
        $test12min   = $parseJson($profile?->test_12min, []);
        $injuries    = $parseJson($profile?->injuries, []);       // voorbeeld: vrije velden
        $preferences = $parseJson($profile?->preferences, []);    // voorbeeld: vrije velden

        // Afgeleiden: hr_max / hr_rest uit heartrate of losse kolommen indien aanwezig
        $hrMax  = $heartrate['max']      ?? $heartrate['hr_max']  ?? ($profile->hr_max_bpm  ?? null);
        $hrRest = $heartrate['resting']  ?? $heartrate['rest']    ?? $heartrate['hr_rest'] ?? ($profile->rest_hr_bpm ?? null);

        // Cooper afstand (meters)
        $cooperMeters = isset($test12min['meters']) ? (int) $test12min['meters'] : null;

        return view('coach.clients.intake', compact(
            'client',
            'profile',
            'birthdate',
            'ageYears',
            'address',
            'heartrate',
            'test12min',
            'injuries',
            'preferences',
            'hrMax',
            'hrRest',
            'cooperMeters'
        ));
    }

    public function claim(Request $request)
    {
        $q = trim($request->input('q', ''));

        $profiles = ClientProfile::query()
            ->whereNull('coach_id')
            ->whereHas('user', function ($q2) {
                $q2->where('role', 'client'); // alleen klanten
            })
            ->when($q !== '', function ($query) use ($q) {
                // zoeken op naam / e-mail van de gekoppelde user
                $query->whereHas('user', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->with(['user:id,name,email']) // eager load zodat we in de view $profile->user->... kunnen gebruiken
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('coach.clients.claim', [
            'profiles' => $profiles,
            'q'        => $q,
        ]);
    }

    public function claimStore(Request $request, ClientProfile $profile)
    {
        $updated = ClientProfile::query()
            ->where('id', $profile->id)
            ->whereNull('coach_id')
            ->update([
                'coach_id'   => $request->user()->id,
                'updated_at' => now(),
            ]);

        if (! $updated) {
            return back()->with('error', 'Deze klant is al geclaimd door een andere coach.');
        }

        return back()->with('success', 'Klant succesvol aan jou toegewezen.');
    }
}
