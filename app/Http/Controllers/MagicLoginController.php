<?php

namespace App\Http\Controllers;

use App\Mail\MagicLoginCode;
use App\Models\User;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MagicLoginController extends Controller
{
    /** Loginpagina tonen; ingelogden direct doorsturen op basis van rol/intake */
    public function show()
    {
        if (auth()->check()) {
            $user = auth()->user();

            // ➜ Intake alleen voor CLIENTS
            if ($user->role === 'client') {
                $step = $this->firstMissingStep($user);

                if (!is_null($step)) {
                    return redirect()
                        ->route('intake.index', ['step' => $step])
                        ->with('fill_missing', true);
                }
            }

            return redirect()->to($this->destinationFor($user));
        }

        return view('auth.login');
    }

    /** Code aanvragen (via Mailgun) */
    public function requestCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = trim(mb_strtolower($validated['email']));
        $user  = User::where('email', $email)->first();

        // Altijd generiek antwoord → geen user enumeration
        $generic = back()
            ->with('status', 'We hebben je inlogcode verstuurd naar het opgegeven e-mailadres.')
            ->with('login_step', 'verify')
            ->withInput(['email' => $email]);

        if (!$user) {
            Log::info("[Login] Code aangevraagd voor onbekend e-mailadres: {$email}");
            return $generic;
        }

        // Genereer 6-cijfer code en zet 10 min geldig
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->temp_code            = $code; // Tip: hash deze indien gewenst
        $user->temp_code_expires_at = now()->addMinutes(10);
        $user->save();

        Log::info("[Login] Code aangevraagd voor {$user->email} (geldig t/m {$user->temp_code_expires_at})");

        // Verstuur via Mailgun (Laravel Mail)
        try {
            Mail::to($user->email)->send(
                new MagicLoginCode(code: $code, ttlMinutes: 10)
            );
            Log::info('[Login] Magic code gemaild via Mailgun', ['email' => $user->email]);
        } catch (\Throwable $e) {
            Log::warning('[Login] Mailgun versturen mislukt', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        return $generic;
    }

    /** Code verifiëren en inloggen */
    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'digits:6'],
        ]);

        $email = trim(mb_strtolower($validated['email']));
        $user  = User::where('email', $email)->first();

        if (
            !$user ||
            !$user->temp_code ||
            !$user->temp_code_expires_at ||
            now()->greaterThan($user->temp_code_expires_at) ||
            !hash_equals($user->temp_code, $validated['code'])
        ) {
            if ($user) {
                $this->invalidate($user);
                $user->save();
            }

            return back()
                ->withErrors(['code' => 'Ongeldige of verlopen code.'])
                ->with('login_step', 'verify')
                ->withInput(['email' => $email]);
        }

        // Succes
        $user->last_login_at = now();
        $this->invalidate($user);
        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        // Reset subscription popup en renew status bij nieuwe login
        $request->session()->forget('subscription_popup_shown');
        $request->session()->forget('subscription_renew');

        $user = Auth::user(); // vers ophalen

        // ➜ Intake alleen voor CLIENTS
        if ($user && $user->role === 'client') {
            $step = $this->firstMissingStep($user);
            if (!is_null($step)) {
                return redirect()
                    ->route('intake.index', ['step' => $step])
                    ->with('fill_missing', true);
            }
        }

        return redirect()->intended($this->destinationFor($user));
    }

    /** Uitloggen */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /** Bestemming op basis van rol */
    private function destinationFor(User $user): string
    {
        return match ($user->role) {
            'coach'  => route('coach.index'),
            'client' => route('client.index'),
            default  => url('/'),
        };
    }

    /** Code ongeldig maken */
    private function invalidate(User $user): void
    {
        $user->temp_code            = null;
        $user->temp_code_expires_at = null;
    }

    /** True als er nog een intake-stap open staat (alleen clients) */
    public function requiresIntake(User $user): bool
    {
        // coaches, admins, etc → nooit intake verplicht
        if ($user->role !== 'client') {
            return false;
        }

        return !is_null($this->firstMissingStep($user));
    }

    /**
     * Bepaal op basis van USERS + CLIENT_PROFILES welke intake-stap als eerste mist.
     * Alleen voor clients. Voor coaches altijd null.
     */
    private function firstMissingStep(User $user): ?int
    {
        // alleen clients hebben intake-flow
        if ($user->role !== 'client') {
            return null;
        }

        $profile = ClientProfile::where('user_id', $user->id)->first();

        // Geen profiel → begin bij stap 0
        if (!$profile) {
            return 0;
        }

        $address = is_array($profile->address)    ? $profile->address    : [];
        $goal    = is_array($profile->goal)       ? $profile->goal       : [];
        $freq    = is_array($profile->frequency)  ? $profile->frequency  : [];
        $test12  = is_array($profile->test_12min) ? $profile->test_12min : [];
        $hr      = is_array($profile->heartrate)  ? $profile->heartrate  : [];

        // === STEP 0: Persoonlijke gegevens ===
        if (
            blank($user->name) ||
            blank($user->email) ||
            blank($profile->birthdate) ||
            blank($profile->gender) ||
            blank($profile->phone_e164) ||
            blank($address['street'] ?? null) ||
            blank($address['house_number'] ?? null) ||
            blank($address['postcode'] ?? null)
        ) {
            return 0;
        }

        // === STEP 1: Coach voorkeur ===
        if (blank($profile->coach_preference)) {
            return 1;
        }

        // === STEP 3: Lengte & Gewicht ===
        if (blank($profile->height_cm) || blank($profile->weight_kg)) {
            return 3;
        }

        // === STEP 5: Doelen (JSON kolom `goals`) ===
        $goals = $profile->goals ?: [];
        if (empty($goals)) {
            return 5;
        }

        // === STEP 6: Sessies & duur (JSON kolom `frequency`) ===
        if (
            blank($freq['sessions_per_week'] ?? null) ||
            blank($freq['minutes_per_session'] ?? null)
        ) {
            return 6;
        }

        // === STEP 11: Doelwedstrijd (JSON kolom `goal`) ===
        if (
            blank($goal['distance'] ?? null) ||
            blank($goal['time_hms'] ?? null) ||
            blank($goal['date'] ?? null)
        ) {
            return 11;
        }

        // === STEP 12: Testresultaten (lopen) ===
        if (blank($test12['meters'] ?? null)) {
            return 12;
        }
        if (empty($profile->test_5k)) {
            return 12;
        }

        // === STEP 13: Hartslag (alleen max verplicht) ===
        if (blank($hr['max'] ?? null)) {
            return 13;
        }

        // STEP 14 (FTP) laten we optioneel.

        // Niets mist meer → intake is klaar
        return null;
    }
}