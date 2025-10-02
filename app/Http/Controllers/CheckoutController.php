<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClientProfile;
use App\Models\Intake;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        // 1) Valideer verplichte velden (stap 0 + 1 + 2)
        $data = $request->validate([
            // stap 0
            'name'         => 'required|string|max:120',
            'email'        => 'required|email',
            'phone'        => 'nullable|string|max:50',
            'dob'          => 'required|date',
            'gender'       => 'required|in:man,vrouw',
            'street'       => 'required|string|max:120',
            'house_number' => 'required|string|max:20',
            'postcode'     => 'required|string|max:20',

            // stap 1
            'preferred_coach' => 'required|in:roy,eline,nicky,none',

            // stap 2
            'package'      => 'required|in:pakket_a,pakket_b,pakket_c',
            'duration'     => 'required|in:12,24',

            // overige stappen (optioneel maar we accepteren ze als ze komen)
            'height_cm'          => 'nullable|numeric|min:120|max:250',
            'weight_kg'          => 'nullable|numeric|min:35|max:250',
            'injuries'           => 'nullable|string|max:500',
            'goals'              => 'nullable|string|max:500',
            'max_days_per_week'  => 'nullable|integer|min:1|max:7',
            'session_minutes'    => 'nullable|integer|min:20|max:180',
            'sport_background'   => 'nullable|string|max:500',
            'facilities'         => 'nullable|string|max:500',
            'materials'          => 'nullable|string|max:500',
            'working_hours'      => 'nullable|string|max:500',
            'goal_distance'      => 'nullable|string|max:30',
            'goal_time_hms'      => 'nullable|regex:/^\d{1,2}:\d{2}:\d{2}$/',
            'goal_ref_date'      => 'nullable|date',
            'cooper_meters'      => 'nullable|integer|min:800|max:5000',
            'test_5k_pace'       => 'nullable|regex:/^\d{1,2}:\d{2}$/',
            'test_10k_pace'      => 'nullable|regex:/^\d{1,2}:\d{2}$/',
            'marathon_pace'      => 'nullable|regex:/^\d{1,2}:\d{2}$/',
            'hr_max_bpm'         => 'nullable|integer|min:120|max:220',
            'rest_hr_bpm'        => 'nullable|integer|min:30|max:100',
            'hr_estimate_from_age' => 'nullable|boolean',
            'ftp_mode'           => 'nullable|in:w,wkg',
            'ftp_watt'           => 'nullable|integer|min:80|max:500',
            'ftp_wkg'            => 'nullable|numeric|min:1|max:7.5',
        ]);

        // Bepaal tarieven op basis van pakket en duur (per 4 weken factureren = maandelijks bedrag)
        $per4w = match ($data['package']) {
            'pakket_a' => 50,   // Basis
            'pakket_b' => 75,   // Chasing Goals
            'pakket_c' => 120,  // Elite Hyrox
        };
        if ((int)$data['duration'] === 24) {
            if ($data['package'] === 'pakket_a') $per4w -= 5;   // 45
            if ($data['package'] === 'pakket_b') $per4w -= 5;   // 70
            if ($data['package'] === 'pakket_c') $per4w -= 10;  // 110
        }
        $months          = ((int)$data['duration'] === 12) ? 3 : 6; // looptijd
        $monthlyAmount   = $per4w;
        $unitAmountCents = (int) round($monthlyAmount * 100);

        // 2) DB: user + profiel + intake + order
        [$user, $intake, $order] = DB::transaction(function () use ($data, $unitAmountCents, $months, $monthlyAmount) {
            // 2.1) User vinden of maken (role=client). Als nieuw: random wachtwoord (kan later via "wachtwoord instellen" worden vervangen)
            /** @var \App\Models\User $user */
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'role'              => 'client',
                    'password'          => Hash::make(Str::random(40)),
                    'email_verified_at' => null,
                ]
            );
            // name eventueel updaten
            if ($user->name !== $data['name']) {
                $user->name = $data['name'];
                $user->save();
            }

            // 2.2) Coach-id bepalen op basis van voorkeur
            $coachId = $this->findCoachIdByPreference($data['preferred_coach']); // int|null

            // 2.3) ClientProfile vullen of bijwerken (alleen blijvende profielvelden)
            /** @var \App\Models\ClientProfile $profile */
            $profile = ClientProfile::firstOrNew(['user_id' => $user->id]);

            $profile->coach_id         = $coachId;
            $profile->birthdate        = $data['dob'];
            $profile->gender           = $this->normalizeGender($data['gender']); // m|f

            // Adres + telefoon bewaren in address-json
            $address = is_array($profile->address) ? $profile->address : [];
            $profile->address = array_merge($address, [
                'street'       => $data['street'],
                'house_number' => $data['house_number'],
                'postcode'     => strtoupper(trim($data['postcode'])),
                'phone'        => $data['phone'] ?? null,
                // 'country'    => 'NL',  // zet dit erbij als je wil
            ]);

            // Metingen
            if (isset($data['height_cm'])) $profile->height_cm = $data['height_cm'];
            if (isset($data['weight_kg'])) $profile->weight_kg = $data['weight_kg'];

            // Intake-velden die blijvend nuttig zijn
            $profile->period_weeks = (int) $data['duration'];

            // frequentie JSON
            $freq = is_array($profile->frequency) ? $profile->frequency : [];
            $profile->frequency = array_filter([
                'sessions_per_week'   => $data['max_days_per_week'] ?? null,
                'minutes_per_session' => $data['session_minutes'] ?? null,
            ], fn($v) => !is_null($v));

            // tekstvelden
            if (array_key_exists('sport_background', $data)) $profile->background = $data['sport_background'] ?: null;
            if (array_key_exists('facilities', $data))       $profile->facilities = $data['facilities'] ?: null;
            if (array_key_exists('materials', $data))        $profile->materials  = $data['materials'] ?: null;
            if (array_key_exists('working_hours', $data))    $profile->work_hours = $data['working_hours'] ?: null;

            // heartrate JSON
            $hr = is_array($profile->heartrate) ? $profile->heartrate : [];
            $profile->heartrate = array_filter([
                'resting' => $data['rest_hr_bpm'] ?? null,
                'max'     => $data['hr_max_bpm'] ?? null,
            ], fn($v) => !is_null($v));

            // Cooper-test
            if (isset($data['cooper_meters'])) {
                $profile->test_12min = ['meters' => (int) $data['cooper_meters']];
            }

            // Alleen 5k pace als minutes/seconds object (de kolom heet test_5k en is JSON, dus dit is prima)
            if (!empty($data['test_5k_pace'])) {
                [$m, $s] = $this->parsePaceToMinSec($data['test_5k_pace']);
                $profile->test_5k = ['minutes' => $m, 'seconds' => $s];
            }

            // Doelen en blessures als array in JSON
            if (array_key_exists('goals', $data)) {
                $profile->goals = $this->explodeToArray($data['goals']);
            }
            if (array_key_exists('injuries', $data)) {
                $profile->injuries = $this->explodeToArray($data['injuries']);
            }

            // coach preference als enum op profiel
            $profile->coach_preference = $data['preferred_coach']; // eline|nicky|roy|none

            $profile->save();

            // 2.4) Intake opslaan of bijwerken (bewaar volledige wizard-payload hier)
            $payload = [
                'package'           => $data['package'],
                'duration_weeks'    => (int) $data['duration'],
                'goal'              => [
                    'distance' => $data['goal_distance'] ?? null,
                    'time_hms' => $data['goal_time_hms'] ?? null,
                    'date'     => $data['goal_ref_date'] ?? null,
                ],
                'run_paces'         => [
                    'p5k'      => $data['test_5k_pace'] ?? null,
                    'p10k'     => $data['test_10k_pace'] ?? null,
                    'marathon' => $data['marathon_pace'] ?? null,
                ],
                'ftp'               => [
                    'mode' => $data['ftp_mode'] ?? null,
                    'watt' => $data['ftp_watt'] ?? null,
                    'wkg'  => $data['ftp_wkg'] ?? null,
                ],
                // Bewaar ook de ruwe contactbasis voor referentie
                'contact'           => [
                    'name'         => $data['name'],
                    'email'        => $data['email'],
                    'phone'        => $data['phone'] ?? null,
                    'dob'          => $data['dob'],
                    'gender'       => $data['gender'],
                    'street'       => $data['street'],
                    'house_number' => $data['house_number'],
                    'postcode'     => $data['postcode'],
                    'preferred_coach' => $data['preferred_coach'],
                ],
            ];

            // Huidige open intake voor deze client pakken, anders nieuwe
            $intake = Intake::where('client_id', $user->id)
                ->whereIn('status', ['draft', 'active'])
                ->latest('id')
                ->first();

            if (!$intake) {
                $intake = new Intake();
                $intake->client_id = $user->id;
                $intake->status    = 'active';
                $intake->payload   = $payload;
                $intake->save();
            } else {
                $intake->status  = $intake->status ?? 'active';
                $intake->payload = array_replace_recursive($intake->payload ?? [], $payload);
                $intake->save();
            }

            // 2.5) Order klaarzetten (pending). We bewaren het maandbedrag.
            $order = new Order();
            $order->client_id     = $user->id;
            $order->intake_id     = $intake->id;
            $order->period_weeks  = (int) $data['duration'];
            $order->amount_cents  = $unitAmountCents; // maandbedrag in centen
            $order->currency      = 'EUR';
            $order->provider      = 'stripe';
            $order->status        = 'pending';
            $order->paid_at       = null;
            $order->save();

            return [$user, $intake, $order];
        });

        // 3) Stripe Checkout sessie aanmaken
        $successUrl = route('intake.index', ['step' => 3, 'advance' => 1]); // door naar Lengte & Gewicht
        $cancelUrl  = route('intake.index', ['step' => 2, 'canceled' => 1]);

        // Testmodus: payments overslaan
        if (config('app.payments_fake', env('PAYMENTS_FAKE', false))) {
            return response()->json(['redirect_url' => $successUrl], 200);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $productName = match ($data['package']) {
            'pakket_a' => '2BeFit - Basis Pakket (maandelijks)',
            'pakket_b' => '2BeFit - Chasing Goals Pakket (maandelijks)',
            'pakket_c' => '2BeFit - Elite Hyrox Pakket (maandelijks)',
        };

        // Let op: geen subscription_data[cancel_at] meesturen (Stripe geeft anders error)
        $session = $stripe->checkout->sessions->create([
            'mode'        => 'subscription',
            'success_url' => $successUrl . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $cancelUrl,
            'customer_email' => $data['email'],
            'locale'      => 'nl',
            'phone_number_collection' => ['enabled' => true],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => [
                        'name' => $productName,
                        'metadata' => [
                            'duration_weeks' => (string) $data['duration'],
                            'monthly_amount' => (string) $monthlyAmount,
                        ],
                    ],
                    'recurring' => [
                        'interval'       => 'month',
                        'interval_count' => 1,
                    ],
                    'unit_amount' => (int) round($monthlyAmount * 100),
                ],
                'quantity' => 1,
            ]],
            'billing_address_collection' => 'required',
            'allow_promotion_codes'      => false,
            'metadata' => [
                'flow'        => '2befit_intake',
                'order_id'    => (string) $order->id,
                'client_id'   => (string) $user->id,
                'intake_id'   => (string) $intake->id,
                'package'     => $data['package'],
                'duration'    => (string) $data['duration'],
            ],
        ]);

        // 4) Order bijwerken met provider_ref (session id), daarna redirect teruggeven
        $order->provider_ref = $session->id;
        $order->save();

        return response()->json(['redirect_url' => $session->url], 201);
    }

    // -------- Helpers --------

    private function normalizeGender(string $g): ?string
    {
        return $g === 'man' ? 'm' : ($g === 'vrouw' ? 'f' : null);
    }

    private function explodeToArray(?string $text): ?array
    {
        if (!$text) return null;
        // splits op ; , of newline en trim
        $parts = preg_split('/[;\n,]+/', $text);
        $clean = array_values(array_filter(array_map(fn($s) => trim($s), $parts)));
        return $clean ?: null;
    }

    /** "MM:SS" → [minutes, seconds] */
    private function parsePaceToMinSec(string $pace): array
    {
        [$m, $s] = array_map('intval', explode(':', $pace));
        $m = max(0, min(59, $m));
        $s = max(0, min(59, $s));
        return [$m, $s];
    }

    /** Vind coach-id op basis van slug: roy|eline|nicky. none → null. */
    private function findCoachIdByPreference(string $pref): ?int
    {
        if ($pref === 'none') return null;

        $map = [
            'roy'   => ['name' => 'Roy',   'email' => 'roy@example.com'],
            'eline' => ['name' => 'Eline', 'email' => 'eline@example.com'],
            'nicky' => ['name' => 'Nicky', 'email' => 'nicky@example.com'],
        ];

        if (!isset($map[$pref])) return null;

        $m = $map[$pref];

        $coach = User::query()
            ->where('role', 'coach')
            ->where(function ($q) use ($m) {
                $q->where('email', $m['email'])
                  ->orWhere('name', $m['name']);
            })
            ->first();

        return $coach?->id;
    }
}
