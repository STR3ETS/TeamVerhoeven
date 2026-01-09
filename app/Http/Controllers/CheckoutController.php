<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClientProfile;
use App\Models\Intake;
use App\Models\Order;
use App\Models\ClientTodoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use MailchimpMarketing\ApiClient as Mailchimp;
use MailchimpMarketing\ApiException as MailchimpApiException;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewIntakeNotification;
use App\Mail\ClientWelcomeMail;

class CheckoutController extends Controller
{
    /**
     * Start checkout:
     * - Valideert intake basisvelden (stap 0–2)
     * - Maakt een DRAFT intake (client_id = null) + pending order
     * - Slaat intake-id in de sessie (voor /intake/progress tijdens wizard)
     * - Start Stripe Checkout en geeft redirect_url terug
     */
    public function create(Request $request)
    {
        Log::info('[checkout.create] start', [
            'ip'            => $request->ip(),
            'payments_fake' => (bool) config('app.payments_fake', env('PAYMENTS_FAKE', false)),
        ]);

        // 1) Validatie (stap 0 + 1 + 2)
        $data = $request->validate([
            // stap 0
            'name'         => 'required|string|max:120',
            'email'        => 'required|email',
            'phone'        => 'nullable|string|max:50',
            'coach_id'     => 'nullable|exists:users,id,role,coach',
            'dob'          => 'required|date',
            'gender'       => 'required|in:man,vrouw',
            'street'       => 'required|string|max:120',
            'house_number' => 'required|string|max:20',
            'postcode'     => 'required|string|max:20',
            'start_date'   => 'required|date|after_or_equal:today',
            // stap 1
            'preferred_coach' => 'required|in:roy,eline,nicky,none',
            // stap 2
            'package'      => 'required|in:pakket_a,pakket_b,pakket_c',
            'duration'     => 'required|in:12,24',
            // overige (optioneel)
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
            'goal_time_hms'      => 'nullable|regex:/^\d{1,2}:\d{2}:\d{2}$/', // HH:MM:SS
            'goal_ref_date'      => 'nullable|date',
            'cooper_meters'      => 'nullable|integer|min:800|max:5000',
            'test_5k_pace'       => 'nullable|regex:/^\d{1,2}:\d{2}$/',        // MM:SS
            'test_10k_pace'      => 'nullable|regex:/^\d{1,2}:\d{2}$/',        // MM:SS
            'marathon_pace'      => 'nullable|regex:/^\d{1,2}:\d{2}:\d{2}$/',  // HH:MM:SS
            'hr_max_bpm'         => 'nullable|integer|min:120|max:220',
            'rest_hr_bpm'        => 'nullable|integer|min:30|max:100',
            'hr_estimate_from_age' => 'nullable|boolean',
            'ftp_mode'           => 'nullable|in:w,wkg',
            'ftp_watt'           => 'nullable|integer|min:80|max:500',
            'ftp_wkg'            => 'nullable|numeric|min:1|max:7.5',
        ]);

        // [AK] Als er een geldige access key in de sessie staat, forceer pakket + duur
        $ak = session('ak'); // ['id'=>..., 'package'=>..., 'duration'=>...]
        if ($ak && isset($ak['package'], $ak['duration'])) {
            $data['package']  = $ak['package'];
            $data['duration'] = (string) $ak['duration']; // validatie verwacht '12' of '24'
            Log::info('[checkout.create] access key forces selection', [
                'package'  => $data['package'],
                'duration' => (int) $data['duration'],
                'ak_id'    => $ak['id'] ?? null,
            ]);
        }

        Log::info('[checkout.create] validated', [
            'email'    => $data['email'],
            'package'  => $data['package'],
            'duration' => (int) $data['duration'],
        ]);

        // 2) Tarieven
        $per4w = match ($data['package']) {
            'pakket_a' => 65,
            'pakket_b' => 90,
            'pakket_c' => 140,
        };
        if ((int)$data['duration'] === 24) {
            if ($data['package'] === 'pakket_a') $per4w -= 5;   // 60
            if ($data['package'] === 'pakket_b') $per4w -= 10;   // 80
            if ($data['package'] === 'pakket_c') $per4w -= 10;  // 130
        }
        $monthlyAmount   = $per4w;
        $unitAmountCents = (int) round($monthlyAmount * 100);

        $coachId = $data['coach_id'] ?? $this->findCoachIdByPreference($data['preferred_coach'] ?? 'none');

        // 3) Payload voor Intake
        $payload = [
            'package'        => $data['package'],
            'duration_weeks' => (int) $data['duration'],
            'contact'        => [
                'name'             => $data['name'],
                'email'            => $data['email'],
                'phone'            => $data['phone'] ?? null,
                'coach_id'         => $coachId ?? null,
                'dob'              => $data['dob'],
                'gender'           => $data['gender'],
                'street'           => $data['street'],
                'house_number'     => $data['house_number'],
                'postcode'         => strtoupper(trim($data['postcode'])),
                'preferred_coach'  => $data['preferred_coach'],
            ],
            'goal'     => [
                'distance' => $data['goal_distance'] ?? null,
                'time_hms' => $data['goal_time_hms'] ?? null,
                'date'     => $data['goal_ref_date'] ?? null,
            ],
            'run_paces'=> [
                'p5k'      => $data['test_5k_pace'] ?? null,
                'p10k'     => $data['test_10k_pace'] ?? null,
                'marathon' => $data['marathon_pace'] ?? null,
            ],
            'ftp'      => [
                'mode' => $data['ftp_mode'] ?? 'w',
                'watt' => $data['ftp_watt'] ?? null,
                'wkg'  => $data['ftp_wkg'] ?? null,
            ],
            'profile'  => [
                'height_cm'         => $data['height_cm'] ?? null,
                'weight_kg'         => $data['weight_kg'] ?? null,
                'injuries'          => $data['injuries'] ?? null,
                'goals'             => $data['goals'] ?? null,
                'max_days_per_week' => $data['max_days_per_week'] ?? null,
                'session_minutes'   => $data['session_minutes'] ?? null,
                'sport_background'  => $data['sport_background'] ?? null,
                'facilities'        => $data['facilities'] ?? null,
                'materials'         => $data['materials'] ?? null,
                'working_hours'     => $data['working_hours'] ?? null,
                'cooper_meters'     => $data['cooper_meters'] ?? null,
                'hr_max_bpm'        => $data['hr_max_bpm'] ?? null,
                'rest_hr_bpm'       => $data['rest_hr_bpm'] ?? null,
                'test_5k_pace'      => $data['test_5k_pace'] ?? null,
                'test_10k_pace'     => $data['test_10k_pace'] ?? null,
                'marathon_pace'     => $data['marathon_pace'] ?? null,
                'goal_distance'     => $data['goal_distance'] ?? null,
                'goal_time_hms'     => $data['goal_time_hms'] ?? null,
                'goal_ref_date'     => $data['goal_ref_date'] ?? null,
                'ftp_mode'          => $data['ftp_mode'] ?? 'w',
                'ftp_watt'          => $data['ftp_watt'] ?? null,
                'ftp_wkg'           => $data['ftp_wkg'] ?? null,
            ],
        ];

        // 4) Draft Intake + pending Order
        [$intake, $order] = DB::transaction(function () use ($payload, $data, $unitAmountCents) {
            $intake = Intake::create([
                'client_id'  => null,
                'status'     => 'active',
                'payload'    => $payload,
                'start_date' => $data['start_date'],   // 👈 HIER OPSLAAN
            ]);

            $order = Order::create([
                'client_id'    => null,
                'intake_id'    => $intake->id,
                'period_weeks' => (int) $data['duration'],
                'amount_cents' => $unitAmountCents,
                'currency'     => 'EUR',
                'provider'     => 'stripe',
                'status'       => 'pending',
                'paid_at'      => null,
            ]);

            return [$intake, $order];
        });

        Log::info('[checkout.create] draft created', [
            'intake_id'    => $intake->id,
            'order_id'     => $order->id,
            'amount_cents' => $unitAmountCents,
        ]);

        // in sessie (ook in FAKE)
        session(['draft_intake_id' => $intake->id]);

        // 5) FAKE flow forceren als er een access key is, of globale FAKE aan staat
        $forceFake = (bool) ($ak && isset($ak['id']));
        if ($forceFake || config('app.payments_fake', env('PAYMENTS_FAKE', false))) {

            Log::info('[checkout.create] FAKE enabled (access key or global)');

            DB::transaction(function () use ($intake, $order, $data, $ak, $forceFake) {
                // User
                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'name'     => $data['name'] ?? 'Nieuwe klant',
                        'role'     => 'client',
                        'password' => Hash::make(Str::random(40)),
                    ]
                );

                // Intake koppelen
                if (!$intake->client_id) {
                    $intake->client_id = $user->id;
                    $intake->save();
                }

                // Order -> paid
                $order->client_id    = $user->id;
                $order->status       = 'paid';
                $order->paid_at      = now();
                $order->provider_ref = $forceFake ? ('access_key_'.$ak['id']) : ('fake_'.$order->id);
                $order->save();

                // ClientProfile vullen
                $contact = $intake->payload['contact'] ?? [];
                $p       = $intake->payload['profile'] ?? [];
                $goal    = $intake->payload['goal'] ?? [];

                $profile = ClientProfile::firstOrNew(['user_id' => $user->id]);
                $profile->birthdate        = $contact['dob'] ?? $profile->birthdate;
                if (empty($profile->coach_id)) {
                    $profile->coach_id = ($contact['coach_id'] ?? null)
                        ?: $this->findCoachIdByPreference($contact['preferred_coach'] ?? 'none');
                }
                $profile->gender           = $this->normalizeGender($contact['gender'] ?? null) ?? $profile->gender;
                $profile->coach_preference = $contact['preferred_coach'] ?? $profile->coach_preference ?? 'none';
                $profile->period_weeks     = (int)($intake->payload['duration_weeks'] ?? $profile->period_weeks ?? 12);

                $existingAddress = is_array($profile->address) ? $profile->address : [];
                $profile->address = array_merge($existingAddress, array_filter([
                    'street'       => $contact['street'] ?? null,
                    'house_number' => $contact['house_number'] ?? null,
                    'postcode'     => $contact['postcode'] ?? null,
                ], fn ($v) => !is_null($v)));

                if (!empty($contact['phone'])) {
                    $profile->phone_e164 = $contact['phone'];
                }

                $profile->height_cm = $p['height_cm'] ?? $profile->height_cm;
                $profile->weight_kg = $p['weight_kg'] ?? $profile->weight_kg;

                $existingFrequency = is_array($profile->frequency) ? $profile->frequency : [];
                $profile->frequency = array_merge($existingFrequency, array_filter([
                    'sessions_per_week'   => $p['max_days_per_week'] ?? null,
                    'minutes_per_session' => $p['session_minutes'] ?? null,
                ], fn ($v) => !is_null($v)));

                $profile->background = $p['sport_background'] ?? $profile->background;
                $profile->facilities = $p['facilities'] ?? $profile->facilities;
                $profile->materials  = $p['materials'] ?? $profile->materials;
                $profile->work_hours = $p['working_hours'] ?? $profile->work_hours;

                $existingHR = is_array($profile->heartrate) ? $profile->heartrate : [];
                $profile->heartrate = array_merge($existingHR, array_filter([
                    'resting' => $p['rest_hr_bpm'] ?? null,
                    'max'     => $p['hr_max_bpm'] ?? null,
                ], fn ($v) => !is_null($v)));

                if (!empty($p['cooper_meters'])) $profile->test_12min = ['meters' => (int)$p['cooper_meters']];
                if (!empty($p['test_5k_pace']))  $profile->test_5k    = $this->paceToJson($p['test_5k_pace']);
                if (!empty($p['test_10k_pace'])) $profile->test_10k   = $this->paceToJson($p['test_10k_pace']);
                if (!empty($p['marathon_pace'])) $profile->test_marathon = $this->hmsToJson($p['marathon_pace']);

                if (!empty($p['goals']))    $profile->goals    = $this->explodeToArray($p['goals']);
                if (!empty($p['injuries'])) $profile->injuries = $this->explodeToArray($p['injuries']);

                if (!empty($goal)) {
                    $profile->goal = array_filter([
                        'distance' => $goal['distance'] ?? null,
                        'time_hms' => $goal['time_hms'] ?? null,
                        'date'     => $goal['date'] ?? null,
                    ], fn ($v) => !is_null($v) && $v !== '');
                }

                $ftpMode = $p['ftp_mode'] ?? 'w';
                $ftpWatt = $p['ftp_watt'] ?? null;
                $ftpWkg  = $p['ftp_wkg'] ?? null;
                if ($ftpMode === 'w' && $ftpWatt && ($profile->weight_kg ?? null)) {
                    $ftpWkg = round($ftpWatt / (float)$profile->weight_kg, 2);
                }
                $profile->ftp = array_filter([
                    'mode' => in_array($ftpMode, ['w','wkg'], true) ? $ftpMode : 'w',
                    'watt' => $ftpWatt ? (int) $ftpWatt : null,
                    'wkg'  => $ftpWkg  ? (float)$ftpWkg  : null,
                ], fn($v) => !is_null($v));

                $profile->save();

                $pkg = (string)($intake->payload['package'] ?? 'pakket_a');
                $dur = (int)($intake->payload['duration_weeks'] ?? 12);
                $this->seedDefaultTodosForClient($user->id, $pkg, $dur);

                // 🔔 NIEUW: mail sturen als het echt een nieuwe user is
                $this->notifyNewUserIfNeeded($user, $intake, $order); // <-- deze regel toevoegen

                // [AK] gebruiks­count ophogen
                if ($forceFake) {
                    \App\Models\AccessKey::where('id', $ak['id'])->increment('uses_count');
                }
            });

            // Mailchimp sync NA commit (FAKE als "paid")
            try {
                $contact = $intake->payload['contact'] ?? [];
                $mcEmail = $contact['email'] ?? $data['email'] ?? null;

                $merge = [
                    'FNAME' => $contact['name'] ?? ($data['name'] ?? null),
                    'PAKKET'=> $intake->payload['package'] ?? null,
                    'DUUR'  => $intake->payload['duration_weeks'] ?? null,
                ];

                $tags = array_filter([
                    env('MAILCHIMP_WELCOME_TAG', '2BeFit: Paid'),
                    'paid',
                    $intake->payload['package'] ?? null, // pakket_a/b/c
                ]);

                Log::info('[checkout.create] scheduling mailchimp sync', [
                    'email'  => $mcEmail,
                    'merge'  => $merge,
                    'tags'   => $tags,
                    'reason' => 'FAKE paid',
                ]);
                DB::afterCommit(function () use ($mcEmail, $merge, $tags) {
                    $this->syncMailchimpContact($mcEmail, $merge, $tags);
                });
            } catch (\Throwable $e) {
                Log::warning('[checkout.create] mailchimp schedule failed', ['e' => $e->getMessage()]);
            }

            // Opruimen
            if ($forceFake) {
                session()->forget('ak'); // key niet hergebruiken binnen dezelfde sessie
            }

            // Door naar intake (stap 3) zonder Stripe
            return response()->json([
                'redirect_url' => route('intake.index', ['step' => 2, 'advance' => 1]),
            ], 200);
        }

        // 6) Echte Stripe flow
        $successUrl = route('intake.index', ['step' => 2, 'advance' => 1]) . '&session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = route('intake.index', ['step' => 2, 'canceled' => 1]);

        $stripe = new StripeClient(config('services.stripe.secret'));
        $productName = match ($data['package']) {
            'pakket_a' => '2BeFit - Basis Pakket (maandelijks)',
            'pakket_b' => '2BeFit - Chasing Goals Pakket (maandelijks)',
            'pakket_c' => '2BeFit - Elite Hyrox Pakket (maandelijks)',
        };

        $session = $stripe->checkout->sessions->create([
            'mode'        => 'subscription',
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'customer_email' => $data['email'],
            'locale'      => 'nl',
            'payment_method_types' => [
                'card',
                'ideal',
                // eventueel:
                // 'sepa_debit',
            ],
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
                    'recurring'  => ['interval' => 'month', 'interval_count' => 1],
                    'unit_amount'=> $unitAmountCents,
                ],
                'quantity' => 1,
            ]],
            'billing_address_collection' => 'required',
            'allow_promotion_codes'      => false,
            'metadata' => [
                'flow'       => '2befit_intake',
                'order_id'   => (string) $order->id,
                'intake_id'  => (string) $intake->id,
                'package'    => $data['package'],
                'duration'   => (string) $data['duration'],
                'email'      => $data['email'],
            ],
        ]);

        $order->update(['provider_ref' => $session->id]);

        Log::info('[checkout.create] stripe session created', [
            'session_id' => $session->id,
            'url'        => $session->url,
        ]);

        return response()->json(['redirect_url' => $session->url], 201);
    }

    /**
     * Bevestig betaling na terugkomst van Stripe.
     */
    public function confirm(Request $request)
    {
        Log::info('[checkout.confirm] start', [
            'session_id' => $request->input('session_id'),
            'ip'         => $request->ip(),
        ]);

        $validated = $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            $stripe  = new StripeClient(config('services.stripe.secret'));
            $session = $stripe->checkout->sessions->retrieve($validated['session_id'], []);

            Log::info('[checkout.confirm] stripe session', [
                'id'              => $session->id ?? null,
                'mode'            => $session->mode ?? null,
                'status'          => $session->status ?? null,
                'payment_status'  => $session->payment_status ?? null,
                'customer_email'  => $session->customer_details?->email ?? $session->customer_email ?? null,
                'metadata'        => $session->metadata ?? [],
            ]);

            if (($session->payment_status ?? null) !== 'paid') {
                Log::warning('[checkout.confirm] not paid', [
                    'session_id'     => $session->id ?? null,
                    'payment_status' => $session->payment_status ?? null,
                ]);
                return response()->json(['message' => 'Betaling niet bevestigd.'], 422);
            }

            $intakeId = (int)($session->metadata['intake_id'] ?? 0);
            $orderId  = (int)($session->metadata['order_id']  ?? 0);
            $email    = $session->customer_details?->email
                        ?? $session->customer_email
                        ?? ($session->metadata['email'] ?? null);

            if (!$intakeId || !$email) {
                Log::error('[checkout.confirm] missing metadata/email', [
                    'intake_id' => $intakeId,
                    'order_id'  => $orderId,
                    'email'     => $email,
                    'metadata'  => $session->metadata ?? [],
                ]);
                return response()->json(['message' => 'Onvolledige betaaldata (intake/email).'], 422);
            }

            [$user, $intake, $order] = DB::transaction(function () use ($intakeId, $orderId, $email) {
                $intake = Intake::lockForUpdate()->find($intakeId);
                if (!$intake) {
                    Log::error('[checkout.confirm.tx] intake not found', ['intake_id' => $intakeId]);
                    abort(404, 'Intake niet gevonden.');
                }

                $order = $orderId ? Order::lockForUpdate()->find($orderId) : null;

                // 1) User
                $nameFromPayload = $intake->payload['contact']['name'] ?? 'Nieuwe klant';
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'     => $nameFromPayload,
                        'role'     => 'client',
                        'password' => Hash::make(Str::random(40)),
                    ]
                );

                // 2) Intake koppelen
                if (!$intake->client_id) {
                    $intake->client_id = $user->id;
                    $intake->save();
                }

                // 3) Order koppelen + betalen
                if ($order) {
                    $order->client_id = $user->id;
                    $order->status    = 'paid';
                    $order->paid_at   = now();
                    $order->save();
                }

                // 4) ClientProfile invullen/mergen
                $contact = $intake->payload['contact'] ?? [];
                $p       = $intake->payload['profile'] ?? [];
                $goal    = $intake->payload['goal'] ?? [];

                $profile = ClientProfile::firstOrNew(['user_id' => $user->id]);

                $profile->birthdate        = $contact['dob'] ?? $profile->birthdate;
                if (empty($profile->coach_id)) {
                    $profile->coach_id = ($contact['coach_id'] ?? null)
                        ?: $this->findCoachIdByPreference($contact['preferred_coach'] ?? 'none');
                }
                $profile->gender           = $this->normalizeGender($contact['gender'] ?? null) ?? $profile->gender;
                $profile->coach_preference = $contact['preferred_coach'] ?? $profile->coach_preference ?? 'none';
                $profile->period_weeks     = (int)($intake->payload['duration_weeks'] ?? $profile->period_weeks ?? 12);

                // Adres
                $existingAddress = is_array($profile->address) ? $profile->address : [];
                $profile->address = array_merge($existingAddress, array_filter([
                    'street'       => $contact['street'] ?? null,
                    'house_number' => $contact['house_number'] ?? null,
                    'postcode'     => $contact['postcode'] ?? null,
                ], fn($v) => !is_null($v)));

                // Telefoon
                if (!empty($contact['phone'])) {
                    $profile->phone_e164 = $contact['phone'];
                }

                // Metingen & vrije tekst
                $profile->height_cm = $p['height_cm'] ?? $profile->height_cm;
                $profile->weight_kg = $p['weight_kg'] ?? $profile->weight_kg;

                $existingFrequency = is_array($profile->frequency) ? $profile->frequency : [];
                $profile->frequency = array_merge($existingFrequency, array_filter([
                    'sessions_per_week'   => $p['max_days_per_week'] ?? null,
                    'minutes_per_session' => $p['session_minutes'] ?? null,
                ], fn($v) => !is_null($v)));

                $profile->background = $p['sport_background'] ?? $profile->background;
                $profile->facilities = $p['facilities'] ?? $profile->facilities;
                $profile->materials  = $p['materials'] ?? $profile->materials;
                $profile->work_hours = $p['working_hours'] ?? $profile->work_hours;

                // Hartslag
                $existingHR = is_array($profile->heartrate) ? $profile->heartrate : [];
                $profile->heartrate = array_merge($existingHR, array_filter([
                    'resting' => $p['rest_hr_bpm'] ?? null,
                    'max'     => $p['hr_max_bpm'] ?? null,
                ], fn($v) => !is_null($v)));

                // Tests
                if (!empty($p['cooper_meters'])) $profile->test_12min = ['meters' => (int) $p['cooper_meters']];
                if (!empty($p['test_5k_pace']))  $profile->test_5k    = $this->paceToJson($p['test_5k_pace']);
                if (!empty($p['test_10k_pace'])) $profile->test_10k   = $this->paceToJson($p['test_10k_pace']);
                if (!empty($p['marathon_pace'])) $profile->test_marathon = $this->hmsToJson($p['marathon_pace']);

                if (!empty($p['goals']))    $profile->goals    = $this->explodeToArray($p['goals']);
                if (!empty($p['injuries'])) $profile->injuries = $this->explodeToArray($p['injuries']);

                // Doelwedstrijd → profiel.goal
                if (!empty($goal)) {
                    $profile->goal = array_filter([
                        'distance' => $goal['distance'] ?? null,
                        'time_hms' => $goal['time_hms'] ?? null,
                        'date'     => $goal['date'] ?? null,
                    ], fn ($v) => !is_null($v) && $v !== '');
                }

                // FTP
                $ftpMode = $p['ftp_mode'] ?? 'w';
                $ftpWatt = $p['ftp_watt'] ?? null;
                $ftpWkg  = $p['ftp_wkg'] ?? null;
                if ($ftpMode === 'w' && $ftpWatt && ($profile->weight_kg ?? null)) {
                    $ftpWkg = round($ftpWatt / (float)$profile->weight_kg, 2);
                }
                $profile->ftp = array_filter([
                    'mode' => in_array($ftpMode, ['w','wkg'], true) ? $ftpMode : 'w',
                    'watt' => $ftpWatt ? (int) $ftpWatt : null,
                    'wkg'  => $ftpWkg  ? (float)$ftpWkg  : null,
                ], fn($v) => !is_null($v));

                $profile->save();

                $pkg = (string)($intake->payload['package'] ?? 'pakket_a');
                $dur = (int)($intake->payload['duration_weeks'] ?? 12);
                $this->seedDefaultTodosForClient($user->id, $pkg, $dur);
                $this->notifyNewUserIfNeeded($user, $intake, $order);

                return [$user, $intake, $order];
            });

            Log::info('[checkout.confirm] done', [
                'user_id'      => $user->id,
                'intake_id'    => $intake->id,
                'order_id'     => $order?->id,
                'order_status' => $order?->status,
            ]);

            // Mailchimp sync NA commit (échte betaling)
            try {
                $contact = $intake->payload['contact'] ?? [];
                $mcEmail = $contact['email'] ?? $email ?? null;

                $merge = [
                    'FNAME' => $contact['name'] ?? ($user->name ?? null),
                    'PAKKET'=> $intake->payload['package'] ?? null,
                    'DUUR'  => $intake->payload['duration_weeks'] ?? null,
                ];

                $tags = array_filter([
                    env('MAILCHIMP_WELCOME_TAG', '2BeFit: Paid'),
                    'paid',
                    $intake->payload['package'] ?? null,
                ]);

                Log::info('[checkout.confirm] scheduling mailchimp sync', [
                    'email'  => $mcEmail,
                    'merge'  => $merge,
                    'tags'   => $tags,
                    'reason' => 'REAL paid',
                ]);
                DB::afterCommit(function () use ($mcEmail, $merge, $tags) {
                    $this->syncMailchimpContact($mcEmail, $merge, $tags);
                });
            } catch (\Throwable $e) {
                Log::warning('[checkout.confirm] mailchimp schedule failed', ['e' => $e->getMessage()]);
            }

            return response()->json(['ok' => true]);

        } catch (\Throwable $e) {
            Log::error('[checkout.confirm] exception', [
                'message' => $e->getMessage(),
                'trace'   => substr($e->getTraceAsString(), 0, 2000),
            ]);
            return response()->json(['message' => 'Er ging iets mis bij het bevestigen van de betaling.'], 500);
        }
    }

    /**
     * Tussentijds opslaan van velden uit de wizard.
     */
    public function progress(Request $request)
    {
        $user   = $request->user();
        $intake = null;

        /**
         * 1) Intake ophalen / maken
         *    - Als user ingelogd is → Intake koppelen aan die user (client_id = user->id)
         *    - Anders → oude guest-flow met draft_intake_id uit sessie
         */
        if ($user) {
            $intake = Intake::where('client_id', $user->id)
                ->orderByDesc('id')
                ->first();

            if (!$intake) {
                $intake = Intake::create([
                    'client_id' => $user->id,
                    'status'    => 'active',
                    'payload'   => [],
                ]);

                Log::info('[intake.progress] created new intake for logged-in user', [
                    'intake_id' => $intake->id,
                    'user_id'   => $user->id,
                    'ip'        => $request->ip(),
                ]);
            }
        } else {
            // OUDE GAST-FLOW (zoals je had)
            $intakeId = (int) session('draft_intake_id', 0);

            if ($intakeId) {
                $intake = Intake::find($intakeId);
                if (!$intake) {
                    Log::info('[intake.progress] intake not found for draft_intake_id, creating new draft', [
                        'intake_id' => $intakeId,
                    ]);
                }
            }

            if (!$intake) {
                $intake = Intake::create([
                    'client_id' => null,
                    'status'    => 'active',
                    'payload'   => [],
                ]);

                session(['draft_intake_id' => $intake->id]);

                Log::info('[intake.progress] created new draft intake (guest)', [
                    'intake_id' => $intake->id,
                    'ip'        => $request->ip(),
                ]);
            }
        }

        // 2) Huidige payload
        $payload = $intake->payload ?? [];
        $payload['profile']   = $payload['profile']   ?? [];
        $payload['goal']      = $payload['goal']      ?? [];
        $payload['run_paces'] = $payload['run_paces'] ?? [];
        $payload['ftp']       = $payload['ftp']       ?? [];

        // 3) Alleen bekende keys mergen
        $in = $request->all();

        foreach ([
            'height_cm','weight_kg','injuries','goals','max_days_per_week','session_minutes',
            'sport_background','facilities','materials','working_hours','cooper_meters',
            'hr_max_bpm','rest_hr_bpm','test_5k_pace','test_10k_pace','marathon_pace',
            'ftp_mode','ftp_watt','ftp_wkg','goal_distance','goal_time_hms','goal_ref_date'
        ] as $k) {
            if (array_key_exists($k, $in)) {
                $payload['profile'][$k] = $in[$k];
            }
        }

        foreach (['goal_distance' => 'distance', 'goal_time_hms' => 'time_hms', 'goal_ref_date' => 'date'] as $from => $to) {
            if (array_key_exists($from, $in)) {
                $payload['goal'][$to] = $in[$from];
            }
        }

        foreach (['test_5k_pace' => 'p5k', 'test_10k_pace' => 'p10k', 'marathon_pace' => 'marathon'] as $from => $to) {
            if (array_key_exists($from, $in)) {
                $payload['run_paces'][$to] = $in[$from];
            }
        }

        foreach (['ftp_mode' => 'mode', 'ftp_watt' => 'watt', 'ftp_wkg' => 'wkg'] as $from => $to) {
            if (array_key_exists($from, $in)) {
                $payload['ftp'][$to] = $in[$from];
            }
        }

        // 4) Intake payload opslaan
        $intake->payload = $payload;
        $intake->save();

        /**
         * 5) Spiegel naar ClientProfile
         *    - ingelogd → user->id
         *    - fallback: intake->client_id (voor het geval die er al is)
         */
        $targetUserId = $user?->id ?? $intake->client_id;

        if ($targetUserId) {
            $profile = ClientProfile::firstOrNew(['user_id' => $targetUserId]);

            // blessures / doelen → arrays
            if (array_key_exists('injuries', $in)) {
                $profile->injuries = $this->explodeToArray((string) $in['injuries']);
            }
            if (array_key_exists('goals', $in)) {
                $profile->goals = $this->explodeToArray((string) $in['goals']);
            }

            // metingen
            if (array_key_exists('height_cm', $in)) {
                $profile->height_cm = $in['height_cm'] ?: null;
            }
            if (array_key_exists('weight_kg', $in)) {
                $profile->weight_kg = $in['weight_kg'] ?: null;
            }

            // frequentie
            if (array_key_exists('max_days_per_week', $in) || array_key_exists('session_minutes', $in)) {
                $existingFrequency = is_array($profile->frequency) ? $profile->frequency : [];
                $profile->frequency = array_merge($existingFrequency, array_filter([
                    'sessions_per_week'   => $in['max_days_per_week'] ?? null,
                    'minutes_per_session' => $in['session_minutes']   ?? null,
                ], fn($v) => !is_null($v)));
            }

            // vrije tekst
            if (array_key_exists('sport_background', $in)) {
                $profile->background = $in['sport_background'] ?: null;
            }
            if (array_key_exists('facilities', $in)) {
                $profile->facilities = $in['facilities'] ?: null;
            }
            if (array_key_exists('materials', $in)) {
                $profile->materials = $in['materials'] ?: null;
            }
            if (array_key_exists('working_hours', $in)) {
                $profile->work_hours = $in['working_hours'] ?: null;
            }

            // doelwedstrijd → profiel.goal
            $goalKeysPresent = array_key_exists('goal_distance', $in)
                            || array_key_exists('goal_time_hms', $in)
                            || array_key_exists('goal_ref_date', $in);

            if ($goalKeysPresent) {
                $existingGoal = is_array($profile->goal) ? $profile->goal : [];
                $profile->goal = array_merge($existingGoal, array_filter([
                    'distance' => $in['goal_distance'] ?? null,
                    'time_hms' => $in['goal_time_hms'] ?? null,
                    'date'     => $in['goal_ref_date'] ?? null,
                ], fn ($v) => !is_null($v) && $v !== ''));
            }

            // hartslag
            if (array_key_exists('hr_max_bpm', $in) || array_key_exists('rest_hr_bpm', $in)) {
                $existingHR = is_array($profile->heartrate) ? $profile->heartrate : [];
                $profile->heartrate = array_merge($existingHR, array_filter([
                    'resting' => $in['rest_hr_bpm'] ?? null,
                    'max'     => $in['hr_max_bpm']  ?? null,
                ], fn($v) => !is_null($v)));
            }

            // tests
            if (array_key_exists('cooper_meters', $in) && $in['cooper_meters'] !== null && $in['cooper_meters'] !== '') {
                $profile->test_12min = ['meters' => (int) $in['cooper_meters']];
            }
            if (array_key_exists('test_5k_pace', $in) && $in['test_5k_pace']) {
                $profile->test_5k = $this->paceToJson($in['test_5k_pace']);
            }
            if (array_key_exists('test_10k_pace', $in) && $in['test_10k_pace']) {
                $profile->test_10k = $this->paceToJson($in['test_10k_pace']);
            }
            if (array_key_exists('marathon_pace', $in) && $in['marathon_pace']) {
                $profile->test_marathon = $this->hmsToJson($in['marathon_pace']);
            }

            // FTP bijwerken
            if (array_key_exists('ftp_mode', $in) || array_key_exists('ftp_watt', $in) || array_key_exists('ftp_wkg', $in)) {
                $existingFtp = is_array($profile->ftp) ? $profile->ftp : [];
                $ftpMode = $in['ftp_mode'] ?? ($existingFtp['mode'] ?? 'w');
                $ftpWatt = $in['ftp_watt'] ?? ($existingFtp['watt'] ?? null);
                $ftpWkg  = $in['ftp_wkg']  ?? ($existingFtp['wkg']  ?? null);

                if (($ftpMode === 'w' || !$ftpMode) && $ftpWatt && ($profile->weight_kg ?? null)) {
                    $ftpWkg = round(((float)$ftpWatt) / (float)$profile->weight_kg, 2);
                }

                $profile->ftp = array_filter([
                    'mode' => in_array($ftpMode, ['w','wkg'], true) ? $ftpMode : 'w',
                    'watt' => $ftpWatt !== '' ? ($ftpWatt ? (int)$ftpWatt : null) : null,
                    'wkg'  => $ftpWkg  !== '' ? ($ftpWkg  ? (float)$ftpWkg  : null) : null,
                ], fn($v) => !is_null($v));
            }

            $profile->save();
        }

        return response()->json(['ok' => true]);
    }

    // ----------------- Mailchimp helpers -----------------

    /** Mailchimp client */
    private function mc(): Mailchimp
    {
        $apiKey = env('MAILCHIMP_API_KEY');
        $server = env('MAILCHIMP_SERVER_PREFIX');

        if (!$apiKey || !$server) {
            Log::warning('[mailchimp.config] missing', [
                'has_api_key' => (bool)$apiKey,
                'server'      => $server ?: null,
            ]);
        }

        $mc = new Mailchimp();
        $mc->setConfig([
            'apiKey' => $apiKey,
            'server' => $server,
        ]);

        return $mc;
    }

    /** Upsert contact + merge fields + tags (triggert je Journey) */
    private function syncMailchimpContact(string $email, array $mergeFields = [], array $tags = []): void
    {
        Log::info('[mailchimp.sync] start', [
            'email' => $email,
            'merge' => $mergeFields,
            'tags'  => $tags,
        ]);

        try {
            $listId = env('MAILCHIMP_AUDIENCE_ID');

            if (!$listId || !$email) {
                Log::warning('[mailchimp.sync] missing listId or email', [
                    'has_list_id' => (bool)$listId,
                    'email'       => $email ?: null,
                ]);
                return;
            }

            // Optioneel: filter merge fields naar alleen niet-lege waarden
            $merge = collect($mergeFields)
                ->filter(fn($v) => !is_null($v) && $v !== '')
                ->all();

            $mc = $this->mc();
            $subscriberHash = md5(strtolower($email));

            // Upsert member + merge fields
            $resp1 = $mc->lists->setListMember($listId, $subscriberHash, [
                'email_address' => $email,
                'status_if_new' => 'subscribed', // zorg dat hij echt Subscribed wordt
                'status'        => 'subscribed',
                'merge_fields'  => $merge,
            ]);
            Log::info('[mailchimp.sync] setListMember ok', [
                'email' => $email,
                'id'    => $resp1->id ?? null,
                'status'=> $resp1->status ?? null,
            ]);

            // Apply tags (alleen niet-lege strings)
            $cleanTags = collect($tags)
                ->filter(fn($t) => is_string($t) && trim($t) !== '')
                ->map(fn($t) => ['name' => trim($t), 'status' => 'active'])
                ->values()
                ->all();

            if (!empty($cleanTags)) {
                $mc->lists->updateListMemberTags($listId, $subscriberHash, [
                    'tags' => $cleanTags,
                ]);
                Log::info('[mailchimp.sync] tags ok', [
                    'email' => $email,
                    'tags'  => $cleanTags,
                ]);
            } else {
                Log::info('[mailchimp.sync] no tags to apply', ['email' => $email]);
            }

            Log::info('[mailchimp.sync] done', ['email' => $email]);

        } catch (MailchimpApiException $e) {
            // Mailchimp API-fouten geven vaak nuttige details terug:
            $body = method_exists($e, 'getResponseBody') ? $e->getResponseBody() : null;
            Log::error('[mailchimp.sync] api_exception', [
                'email'   => $email,
                'message' => $e->getMessage(),
                'body'    => $body, // bevat errors zoals "merge field does not exist"
            ]);
        } catch (\Throwable $e) {
            Log::error('[mailchimp.sync] exception', [
                'email'   => $email,
                'message' => $e->getMessage(),
                'trace'   => substr($e->getTraceAsString(), 0, 1500),
            ]);
        }
    }

    // ----------------- Overige helpers -----------------

    private function normalizeGender(?string $g): ?string
    {
        return $g === 'man' ? 'm' : ($g === 'vrouw' ? 'f' : null);
    }

    private function explodeToArray(?string $text): ?array
    {
        if (!$text) return null;
        $parts = preg_split('/[;\n,]+/', $text);
        $clean = array_values(array_filter(array_map(fn($s) => trim($s), $parts)));
        return $clean ?: null;
    }

    /** "MM:SS" → ['minutes'=>m,'seconds'=>s] */
    private function paceToJson(string $pace): array
    {
        [$m, $s] = array_map('intval', explode(':', $pace));
        $m = max(0, min(59, $m));
        $s = max(0, min(59, $s));
        return ['minutes' => $m, 'seconds' => $s];
    }

    /** "HH:MM:SS" → ['hours'=>h,'minutes'=>m,'seconds'=>s] */
    private function hmsToJson(string $hms): array
    {
        [$h, $m, $s] = array_map('intval', explode(':', $hms));
        $h = max(0, min(99, $h));
        $m = max(0, min(59, $m));
        $s = max(0, min(59, $s));
        return ['hours' => $h, 'minutes' => $m, 'seconds' => $s];
    }

    /** Vind coach-id op basis van slug (optioneel) */
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

    private function seedDefaultTodosForClient(int $clientUserId, string $package, int $durationWeeks): void
    {
        $defs = [];
        $pos  = 10; // beginpositie

        // pakket B en C delen
        if (in_array($package, ['pakket_b','pakket_c'], true)) {
            $defs[] = [
                'label'    => '30 min call met coach',
                'optional' => false,
                'notes'    => null,
                'pos'      => $pos,
            ]; $pos += 10;

            if ($package === 'pakket_b') {
                if ($durationWeeks === 24) {
                    // KEUZE → 1 item
                    $defs[] = [
                        'label'    => 'Kies: 1x 2Befit supplement of 1x voedingsplan',
                        'optional' => true,
                        'notes'    => 'Maak één keuze en vink af wanneer geleverd.',
                        'pos'      => $pos,
                    ]; $pos += 10;
                }
            }

            if ($package === 'pakket_c') {
                if ($durationWeeks === 12) {
                    // KEUZE → 1 item
                    $defs[] = [
                        'label'    => 'Kies: 1x 2Befit supplement of 1x voedingsplan',
                        'optional' => true,
                        'notes'    => 'Maak één keuze en vink af wanneer geleverd.',
                        'pos'      => $pos,
                    ]; $pos += 10;
                } elseif ($durationWeeks === 24) {
                    // beide → 2 items
                    $defs[] = [
                        'label'    => '1x 2Befit supplement',
                        'optional' => false,
                        'notes'    => null,
                        'pos'      => $pos,
                    ]; $pos += 10;
                    $defs[] = [
                        'label'    => '1x voedingsplan',
                        'optional' => false,
                        'notes'    => null,
                        'pos'      => $pos,
                    ]; $pos += 10;
                }

                // altijd t-shirt bij pakket C
                $defs[] = [
                    'label'    => 'Gratis 2BeFit workout t-shirt',
                    'optional' => false,
                    'notes'    => null,
                    'pos'      => $pos,
                ]; $pos += 10;
            }
        }

        if (empty($defs)) {
            return; // pakket A → niets
        }

        foreach ($defs as $d) {
            ClientTodoItem::firstOrCreate(
                [
                    'client_user_id' => $clientUserId,
                    'label'          => $d['label'],
                    'source'         => 'system',
                    'package'        => $package,
                    'duration_weeks' => $durationWeeks,
                ],
                [
                    'created_by_user_id' => null,
                    'is_optional'        => (bool)$d['optional'],
                    'position'           => (int)$d['pos'],
                    'notes'              => $d['notes'],
                ]
            );
        }
    }

    /**
     * Stuur mails bij nieuwe user (alleen als hij net is aangemaakt).
     * - Interne notificatie (naar Boyd)
     * - Welkomstmail naar de klant (pakket-afhankelijk)
     */
    private function notifyNewUserIfNeeded(User $user, Intake $intake, ?Order $order = null): void
    {
        if (! $user->wasRecentlyCreated) {
            return;
        }

        try {
            // 1) Interne notificatie (zoals je al had)
            // Mail::to('boyd@eazyonline.nl')
            //     ->send(new NewIntakeNotification($user, $intake, $order));

            // 2) Welkomstmail naar de klant zelf
            if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($user->email)
                    ->send(new ClientWelcomeMail($user, $intake, $order));
            }

            // Coach-specifieke mails op basis van preferred_coach
            $preferred = $intake->payload['contact']['preferred_coach'] ?? 'none';

            /**
             * Logica:
             * - 'nicky'  => alleen Nicky
             * - 'eline'  => alleen Eline
             * - 'roy'    => alleen Roy
             * - 'none'   => alle 3 (Nicky, Eline én Roy)
             */
            $coachEmails = match ($preferred) {
                'nicky' => ['nicky2befitlifestyle@hotmail.com'],
                'eline' => ['eline2befitlifestyle@hotmail.com'],
                'roy'   => ['roy@2befitlifestyle.nl'],
                default => [
                    'nicky2befitlifestyle@hotmail.com',
                    'eline2befitlifestyle@hotmail.com',
                    'roy@2befitlifestyle.nl',
                ],
            };

            foreach ($coachEmails as $coachEmail) {
                Mail::to($coachEmail)->send(new NewIntakeNotification($user, $intake, $order));
            }

        } catch (\Throwable $e) {
            \Log::error('[notifyNewUserIfNeeded] mail failed', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
