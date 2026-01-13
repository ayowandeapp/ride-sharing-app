<?php

namespace App\Http\Controllers;

use App\Events\TripPaymentUpdated;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Stripe\StripeClient;

class TransactionController extends Controller
{
    public function checkout(Request $request, Trip $trip)
    {
        $stripeData = [
            'price_data' => [
                'currency' => config('cashier.currency'),
                'unit_amount' => $trip->transaction->total_cost * 100, // in cents
                'product_data' => [
                    'name' => 'Trip to ' . $trip->destination_name,
                    'metadata' => [
                        'user_id' => $request->user()->id,
                        'trip_id' => $trip->id,
                    ]
                ],
            ],
            'quantity' => 1,
        ];

        $checkout = $request->user()->checkout([$stripeData], [
            'success_url' => config('app.url') . '/api/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.url') . '/api/checkout/cancel',
            'mode' => 'payment',
            'metadata' => [
                'user_id' => $request->user()->id,
                'trip_id' => $trip->id,
            ],
        ]);

        return response()->json([
            'url' => $checkout->url,
            'session_id' => $checkout->id
        ]);
    }

    public function success(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');

            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No session ID provided'
                ], 400);
            }

            $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return;
            }

            $tripId = $session['metadata']['trip_id'] ?? null;

            $trip = Trip::findOrFail($tripId);

            $this->updatePaymentRecord($session, $trip);

            $trip->load(['driver.user', 'transaction']);

            TripPaymentUpdated::dispatch($trip, $trip->user);


            // return response()->json([
            //     'success' => true,
            //     'message' => 'Payment successful!',
            //     // 'trip' => ,
            //     'redirect_url' => '/landing'
            // ]);

        } catch (\Exception $e) {
            Log::error('Stripe success callback error:', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId ?? 'none'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment.',
            ], 500);
        }
    }

    private function updatePaymentRecord($session, Trip $trip)
    {
        $trip->transaction()->update([
            'amount_paid' => $session->amount_total / 100,
            'currency' => strtoupper($session->currency),
            'payment_method' => $session->payment_intent->payment_method_types[0] ?? 'card',
            'status' => $session->status,
            'stripe_response' => json_encode($session->toArray())
        ]);
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Payment was cancelled.',
            'redirect_url' => '/checkout-cancelled'
        ]);
    }

}
