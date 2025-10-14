<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MailchimpMarketing\ApiClient as Mailchimp;

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

    /** Code aanvragen (versturen via Mailchimp tag-trigger) */
    public function requestCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required','email','max:255'],
        ]);

        $email = trim(mb_strtolower($validated['email']));
        $user  = User::where('email', $email)->first();

        // Altijd generiek antwoord → geen user enumeration
        // Zet wel de UI in "verify"-stand en houd e-mail vast.
        $generic = back()
            ->with('status', 'We hebben je inlogcode verstuurd naar het opgegeven e-mailadres.')
            ->with('login_step', 'verify')
            ->withInput(['email' => $email]);

        // Bestaat geen user? Niet onthullen.
        if (!$user) {
            Log::info("[Login] Code aangevraagd voor onbekend e-mailadres: {$email}");
            // (optioneel) Je kan hier tóch een Mailchimp mail sturen als je een “nog geen account?” flow wilt.
            return $generic;
        }

        // (Optioneel) simple rate limit per gebruiker om spam te voorkomen (1 code per 60 sec)
        if ($user->temp_code_expires_at && now()->diffInSeconds($user->temp_code_expires_at, false) > 540) {
            // Als er net een code is gezet (expire 10min), kun je hier een extra limiet doen.
            // Sla over als je dat niet wilt, of pas melding aan.
        }

        // Genereer 6-cijfer code en zet 10 min geldig
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->temp_code = $code;
        $user->temp_code_expires_at = now()->addMinutes(10);
        $user->save();

        Log::info("[Login] Code aangevraagd voor {$user->email} (geldig t/m {$user->temp_code_expires_at})");

        // >>> Mailchimp: upsert + merge fields + (re)taggen om Journey te triggeren
        try {
            $expiresHuman = $user->temp_code_expires_at->timezone(config('app.timezone'))->format('H:i');

            $merge = array_filter([
                'FNAME'     => $user->name ?? null,
                'LOGINCODE' => $code,
                'CODEEXP'   => "Geldig tot {$expiresHuman}",
            ], fn($v) => !is_null($v) && $v !== '');

            $this->sendLoginCodeViaMailchimp(
                email: $user->email,
                mergeFields: $merge,
                tagName: env('MAILCHIMP_LOGIN_TAG', 'Login: Code')
            );

        } catch (\Throwable $e) {
            Log::warning('[Login] Mailchimp versturen mislukt (outer catch)', [
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
            'email' => ['required','email'],
            'code'  => ['required','digits:6'],
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

    // --------------- Mailchimp helpers ----------------

    private function mc(): Mailchimp
    {
        $mc = new Mailchimp();
        $mc->setConfig([
            'apiKey' => env('MAILCHIMP_API_KEY'),
            'server' => env('MAILCHIMP_SERVER_PREFIX'),
        ]);
        return $mc;
    }

    /**
     * Upsert contact + merge fields en (re)tag voor Journey-trigger.
     * Met robuuste logging + mini delay zodat Mailchimp de tag-wissel ziet.
     */
    private function sendLoginCodeViaMailchimp(string $email, array $mergeFields, string $tagName): void
    {
        $listId = env('MAILCHIMP_AUDIENCE_ID');
        if (!$listId || !$email) {
            Log::warning('[Login] Mailchimp misconfig: audience of email ontbreekt', [
                'listId' => $listId,
                'email'  => $email,
            ]);
            return;
        }

        $mc = $this->mc();
        $subscriberHash = md5(strtolower($email));

        try {
            // 1) Upsert (maak aan of update) als SUBSCRIBED met merge fields
            $member = $mc->lists->setListMember($listId, $subscriberHash, [
                'email_address' => $email,
                'status_if_new' => 'subscribed',
                'status'        => 'subscribed',
                'merge_fields'  => $mergeFields,
            ]);
            Log::info('[Login/Mailchimp] upsert OK', [
                'email'     => $email,
                'member_id' => $member->id ?? null,
                'status'    => $member->status ?? null,
            ]);

            // 2) Klein “pulse”-moment zodat active straks als echte wijziging telt
            // (direct inactive->active kan soms als 1 event samenvallen)
            $mc->lists->updateListMemberTags($listId, $subscriberHash, [
                'tags' => [[ 'name' => $tagName, 'status' => 'inactive' ]],
            ]);
            usleep(400000); // 0.4s

            // 3) Active = trigger de Journey
            $mc->lists->updateListMemberTags($listId, $subscriberHash, [
                'tags' => [[ 'name' => $tagName, 'status' => 'active' ]],
            ]);
            Log::info('[Login/Mailchimp] tag pulsed to active', ['email' => $email, 'tag' => $tagName]);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Mailchimp 4xx: body bevat de echte reden (cleaned/forgotten/compliance/unsubscribed, etc.)
            $status = $e->getResponse()?->getStatusCode();
            $body   = (string) $e->getResponse()?->getBody();
            Log::warning('[Login] Mailchimp 4xx', [
                'email'  => $email,
                'status' => $status,
                'body'   => $body,
            ]);
            // Optioneel: rethrow om in logs/prometheus op te vallen
            // throw $e;
        } catch (\Throwable $e) {
            Log::warning('[Login] Mailchimp error', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            // throw $e; // optioneel
        }
    }
}