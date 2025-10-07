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
