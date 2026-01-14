<?php

namespace App\Http\Controllers;

use App\Events\TripPaymentUpdated;
use App\Models\Trip;
use App\Services\PaystackService;
use Illuminate\Http\Request;

use function Illuminate\Log\log;

class PaystackController extends Controller
{
    public function __construct(private PaystackService $paystack)
    {
    }

    public function initialize(Request $request, Trip $trip)
    {
        // $request->validate([
        //     'amount' => 'required'
        // ]);

        $data = [
            'email' => 'magop90797@gopicta.com',
            'amount' => $trip->transaction->total_cost * 100,
            'metadata' => [
                'name' => $trip->user->name,
                'user_id' => $request->user()->id,
                'trip_id' => $trip->id,
            ],
            'callback_url' => config('app.url') . '/api/paystack-checkout/callback',
        ];
        $res = $this->paystack->initializeTransaction($data);

        return response()->json($res);
    }

    public function callback(Request $request)
    {
        dd($request);
    }

    public function webhook(Request $request)
    {
        log($request);

        $payload = $request->getContent();

        $paystackHeader = $request->header('x-paystack-signature');

        if ($this->isValidWebhook($payload, $paystackHeader)) {
            $resData = $request->json('data');
            $eventType = $request->json('event');

            log($resData);

            if ($eventType == 'charge.success') {

                $tripId = $resData['data']['metadata']['trip_id'] ?? null;

                $trip = Trip::findOrFail($tripId);

                $this->updatePaymentRecord($resData, $trip);

                $trip->load(['driver.user', 'transaction']);

                TripPaymentUpdated::dispatch($trip, $trip->user);

            } elseif ($eventType == 'charge.failure') {

            }
        }
        // return response()->json($payload);
    }

    private function isValidWebhook($payload, $signature)
    {
        $computedSignature = hash_hmac('sha512', $payload, config('services.paystack.secret'));
        return $computedSignature == $signature;
    }

    public function verify(Request $request)
    {
        $reference = $request->query('reference');
        $res = $this->paystack->verifyTransaction($reference);

        $resDecode = json_decode(json_encode($res), true);

        log($resDecode);
        //update the transaction table
        if ($resDecode['data']['status'] == 'success') {
            $tripId = $resDecode['data']['metadata']['trip_id'] ?? null;

            $trip = Trip::findOrFail($tripId);


            $this->updatePaymentRecord($resDecode, $trip);

            $trip->load(['driver.user', 'transaction']);

            TripPaymentUpdated::dispatch($trip, $trip->user);
        }
        return response()->json($res);
    }

    private function updatePaymentRecord($resDecode, Trip $trip)
    {
        $data = $resDecode['data'];
        $trip->transaction()->update([
            'amount_paid' => $data['amount'] / 100,
            'currency' => strtoupper($data['currency']),
            'payment_method' => $data['channel'] ?? 'card',
            'status' => $data['status'],
            'stripe_response' => json_encode($resDecode)
        ]);
    }

}
