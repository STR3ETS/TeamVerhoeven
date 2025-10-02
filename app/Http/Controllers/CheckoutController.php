<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'email'        => 'required|email',
            'phone'        => 'nullable|string|max:50',
            'dob'          => 'required|string',
            'gender'       => 'required|in:man,vrouw',
            'street'       => 'required|string|max:120',
            'house_number' => 'required|string|max:20',
            'postcode'     => 'required|string|max:20',
            'package'      => 'required|in:pakket_a,pakket_b,pakket_c',
            'duration'     => 'required|in:12,24',
        ]);

        // Prijs per 4 weken
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

        // Looptijd (12 weken ≈ 3 maanden, 24 weken ≈ 6 maanden)
        $months = ((int)$data['duration'] === 12) ? 3 : 6;

        // Maandbedrag = prijs per 4 weken (factureren maandelijks)
        $monthlyAmount = $per4w;
        $unitAmountCents = (int) round($monthlyAmount * 100);

        // Waarheen terug na succes/cancel:
        $successUrl = route('intake.index', ['step' => 2, 'advance' => 1]); // 1 stap verder
        $cancelUrl  = route('intake.index', ['step' => 1, 'canceled' => 1]); // terug naar kiezen

        // FAKE modus: Stripe overslaan
        if (config('app.payments_fake', env('PAYMENTS_FAKE', false))) {
            return response()->json(['redirect_url' => $successUrl], 200);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $productName = match ($data['package']) {
            'pakket_a' => '2BeFit - Basis Pakket (maandelijks)',
            'pakket_b' => '2BeFit - Chasing Goals Pakket (maandelijks)',
            'pakket_c' => '2BeFit - Elite Hyrox Pakket (maandelijks)',
        };

        $cancelAt = now()->addMonths($months)->timestamp;

        $session = $stripe->checkout->sessions->create([
            'mode'        => 'subscription',
            'success_url' => $successUrl . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $cancelUrl,
            'customer_creation' => 'always',
            'customer_email'    => $data['email'],
            'locale' => 'nl',
            'phone_number_collection' => ['enabled' => true],
            'metadata' => [
                'flow'     => '2befit_intake',
                'package'  => $data['package'],
                'duration' => (string)$data['duration'],
                'name'     => $data['name'],
                'phone'    => $data['phone'] ?? '',
                'dob'      => $data['dob'],
                'gender'   => $data['gender'],
                'street'   => $data['street'],
                'house_number' => $data['house_number'],
                'postcode' => $data['postcode'],
            ],
            'subscription_data' => [
                'cancel_at' => $cancelAt, // auto-stop na 3/6 maanden
                'metadata'  => [
                    'package'  => $data['package'],
                    'duration' => (string)$data['duration'],
                ],
            ],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => [
                        'name' => $productName,
                        'metadata' => [
                            'duration_weeks' => (string)$data['duration'],
                            'monthly_amount' => (string)$monthlyAmount,
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

        return response()->json(['redirect_url' => $session->url], 201);
    }

    // Optioneel, we redirecten toch al rechtstreeks in success/cancel URL’s
    public function success(Request $r) {
        return redirect()->route('intake.index', ['step' => 2, 'advance' => 1]);
    }
    public function cancel() {
        return redirect()->route('intake.index', ['step' => 1, 'canceled' => 1]);
    }
}
