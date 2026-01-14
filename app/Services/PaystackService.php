<?php

namespace App\Services;

use GuzzleHttp\Client;

class PaystackService
{
    protected Client $client;
    protected $secretKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.paystack.payment_url'),
            'headers' => [
                'Authorization' => "Bearer " . config('services.paystack.secret'),
                'Content-Type' => 'application/json'
            ],
            'verify' => false
        ]);
        $this->secretKey = config('services.paystack.secret');
    }

    public function initializeTransaction(array $data)
    {
        $res = $this->client->post('/transaction/initialize', [
            'json' => $data
        ]);

        return json_decode($res->getBody(), true);
    }

    public function verifyTransaction($reference)
    {
        $res = $this->client->get("/transaction/verify/{$reference}");

        return json_decode($res->getBody(), true);
    }
}