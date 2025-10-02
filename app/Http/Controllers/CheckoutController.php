<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Intake;
use App\Models\Order;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        // 1) Validatie
        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'email'        => 'required|email',
            'phone'        => 'nullable|string|max:50',
            'dob'          => 'required|date', // liever date dan string
            'gender'       => 'required|in:man,vrouw',
            'street'       => 'required|string|max:120',
            'house_number' => 'required|string|max:20',
            'postcode'     => 'required|string|max:20',
            'package'      => 'required|in:pakket_a,pakket_b,pakket_c',
            'duration'     => 'required|in:12,24',
        ]);

        // 2) Prijsbepaling per 4 weken (zoals in je Blade)
        $per4w = match ($data['package']) {
            'pakket_a' => 50,   // Basis
            'pakket_b' => 75,   // Chasing Goals
            'pakket_c' => 120,  // Elite Hyrox
        };

        // Korting bij 24 weken
        if ((int)$data['duration'] === 24) {
            if ($data['package'] === 'pakket_a') $per4w -= 5;   // 45
            if ($data['package'] === 'pakket_b') $per4w -= 5;   // 70
            if ($data['package'] === 'pakket_c') $per4w -= 10;  // 110
        }

        // Abonnement is maandelijks → bedrag per 4 weken ≈ maandbedrag
        $monthlyAmount = $per4w;
        $unitAmountCents = (int) round($monthlyAmount * 100);
        $periodWeeks = (int) $data['duration']; // 12 of 24
        $months = $periodWeeks === 12 ? 3 : 6;

        // 3) URLs
        $successUrl = route('intake.index', ['step' => 2, 'advance' => 1]);
        $cancelUrl  = route('intake.index', ['step' => 1, 'canceled' => 1]);

        // 4) DB-opslaan vóór Stripe (transactie)
        [$client, $profile, $intake, $order] = DB::transaction(function () use ($data, $periodWeeks, $unitAmountCents, $months) {

            // a) User (client) zoeken of aanmaken
            /** @var \App\Models\User $client */
            $client = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'role'              => 'client',
                    // Als hij nog niet bestond: zet een init-wachtwoord. Later kan hij resetten via password reset.
                    'password'          => Hash::make(str()->random(24)),
                    'email_verified_at' => now(),
                ]
            );

            // Updaten van naam als die wijzigt
            if ($client->name !== $data['name']) {
                $client->name = $data['name'];
                $client->save();
            }

            // b) ClientProfile upserten
            $genderMap = ['man' => 'm', 'vrouw' => 'f'];
            $profile = ClientProfile::updateOrCreate(
                ['user_id' => $client->id],
                [
                    // coach_id laten we met rust (kan via UI toewijzing elders)
                    'birthdate'        => $data['dob'],
                    'gender'           => $genderMap[$data['gender']] ?? null,
                    'address'          => [
                        'street'       => $data['street'],
                        'house_number' => $data['house_number'],
                        'postcode'     => $data['postcode'],
                    ],
                    'phone_e164'       => $data['phone'] ?? null, // intl-tel-input levert E.164
                    // Laat overige intakevelden (goals, injuries etc.) later via intake-sync invullen
                    // Standaard period_weeks kun je hier ook al op duur zetten:
                    'period_weeks'     => $periodWeeks,
                ]
            );

            // c) Intake aanmaken (actieve intake)
            $payload = [
                'name'         => $data['name'],
                'email'        => $data['email'],
                'phone'        => $data['phone'] ?? null,
                'birthdate'    => $data['dob'],
                'gender'       => $data['gender'], // originele string (man/vrouw)
                'address'      => [
                    'street'       => $data['street'],
                    'house_number' => $data['house_number'],
                    'postcode'     => $data['postcode'],
                ],
                'package'      => $data['package'],
                'duration'     => $periodWeeks,
                'months'       => $months,
            ];

            $intake = Intake::create([
                'client_id'    => $client->id,
                'status'       => 'active',
                'payload'      => $payload,
                'completed_at' => null,
            ]);

            // d) Order aanmaken (pending)
            $order = Order::create([
                'client_id'     => $client->id,
                'intake_id'     => $intake->id,
                'period_weeks'  => $periodWeeks,
                'amount_cents'  => $unitAmountCents,  // maandelijks
                'currency'      => 'EUR',
                'provider'      => 'stripe',
                'provider_ref'  => null,
                'status'        => 'pending',
                'paid_at'       => null,
            ]);

            return [$client, $profile, $intake, $order];
        });

        // 5) Fake-modus? (dev zonder Stripe)
        if (config('app.payments_fake', env('PAYMENTS_FAKE', false))) {
            return response()->json(['redirect_url' => $successUrl], 200);
        }

        // 6) Stripe Checkout (subscription)
        $stripe = new StripeClient(config('services.stripe.secret'));

        $productName = match ($data['package']) {
            'pakket_a' => '2BeFit - Basis Pakket (maandelijks)',
            'pakket_b' => '2BeFit - Chasing Goals Pakket (maandelijks)',
            'pakket_c' => '2BeFit - Elite Hyrox Pakket (maandelijks)',
        };

        // Belangrijk: geen subscription_data[cancel_at] meegeven → gaf eerder errors.
        $session = $stripe->checkout->sessions->create([
            'mode'        => 'subscription',
            'success_url' => $successUrl . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $cancelUrl,
            'customer_email' => $data['email'],
            'locale' => 'nl',
            'phone_number_collection' => ['enabled' => true],
            'metadata' => [
                'flow'        => '2befit_intake',
                'order_id'    => (string)$order->id,
                'client_id'   => (string)$client->id,
                'intake_id'   => (string)$intake->id,
                'package'     => $data['package'],
                'duration'    => (string)$periodWeeks,
                'monthly_eur' => (string)$monthlyAmount,
            ],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => [
                        'name'     => $productName,
                        'metadata' => [
                            'duration_weeks' => (string)$periodWeeks,
                        ],
                    ],
                    'recurring' => [
                        'interval' => 'month',
                        'interval_count' => 1,
                    ],
                    'unit_amount' => $unitAmountCents,
                ],
                'quantity' => 1,
            ]],
            'billing_address_collection' => 'required',
            'allow_promotion_codes' => false,
        ]);

        // 7) Provider ref vastleggen (niet betaald, maar wel sessie)
        $order->update(['provider_ref' => $session->id]);

        return response()->json(['redirect_url' => $session->url], 201);
    }

    public function success(Request $request)
    {
        return redirect()->route('intake.index', ['step' => 2, 'advance' => 1]);
    }

    public function cancel()
    {
        return redirect()->route('intake.index', ['step' => 1, 'canceled' => 1]);
    }
}
