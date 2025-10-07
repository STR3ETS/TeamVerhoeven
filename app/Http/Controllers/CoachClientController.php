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
}
