<?php

namespace App\Http\Controllers;

use App\Mail\MagicLoginCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MagicLoginController extends Controller
{
    /** Loginpagina tonen; ingelogden direct doorsturen op basis van rol */
    public function show()
    {
        if (auth()->check()) {
            return redirect()->to($this->destinationFor(auth()->user()));
        }
        return view('auth.login');
    }

    /** Code aanvragen (nu via Mailgun i.p.v. Mailchimp) */
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

        // (Optioneel) throttling: max 1 nieuwe code per minuut e.d. (hier alleen voorbeeld)
        // if ($user->temp_code_expires_at && now()->diffInSeconds($user->temp_code_expires_at, false) > 540) {
        //     return $generic; // of eigen melding
        // }

        // Genereer 6-cijfer code en zet 10 min geldig
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->temp_code = $code; // Tip: hash deze indien gewenst
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
        $user->temp_code = null;
        $user->temp_code_expires_at = null;
    }
}