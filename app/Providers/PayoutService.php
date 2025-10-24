<?php

namespace App\Providers;

use GuzzleHttp\Client;

class PayoutService
{
    protected $client;
    protected $keyId;
    protected $keySecret;

    public function __construct()
    {
        $this->keyId = config('services.razorpay.key_id');
        $this->keySecret = config('services.razorpay.key_secret');
        $this->client = new Client([
            'base_uri' => 'https://api.razorpay.com/',
            'auth' => [$this->keyId, $this->keySecret],
            'timeout' => 30,
        ]);
    }

    public function initiateRazorpayxPayout($payout)
    {
        $beneficiary = $payout->beneficiary_snapshot;
        $method = strtoupper($payout->method);

        // 1) Create Contact
        $contactResp = $this->client->post('v1/contacts', [
            'json' => [
                'name' => $beneficiary['name'],
                'type' => 'vendor'
            ],
            'headers' => [
                'Idempotency-Key' => $payout->idempotency_key
            ]
        ]);
        $contact = json_decode($contactResp->getBody()->getContents(), true);

        // 2) Create Fund Account (bank or UPI)
        if ($method === 'UPI') {
            $fundPayload = [
                'contact_id' => $contact['id'],
                'account_type' => 'vpa',
                'vpa' => [
                    'address' => $beneficiary['upi']
                ]
            ];
        } else {
            $fundPayload = [
                'contact_id' => $contact['id'],
                'account_type' => 'bank_account',
                'bank_account' => [
                    'name' => $beneficiary['name'],
                    'ifsc' => $beneficiary['ifsc'],
                    'account_number' => $beneficiary['account']
                ]
            ];
        }

        $fundResp = $this->client->post('v1/fund_accounts', [
            'json' => $fundPayload,
            'headers' => ['Idempotency-Key' => $payout->idempotency_key]
        ]);
        $fund = json_decode($fundResp->getBody()->getContents(), true);

        // 3) Create Payout
        $createResp = $this->client->post('v1/payouts', [
            'json' => [
                'account_number' => config('services.razorpay.account_number'),
                'fund_account_id' => $fund['id'],
                'amount' => intval($payout->amount * 100),
                'currency' => $payout->currency ?? 'INR',
                'mode' => $method,
                'purpose' => 'payout',
                'narration' => 'Seller payout #' . $payout->id,
            ],
            'headers' => [
                'Idempotency-Key' => $payout->idempotency_key
            ]
        ]);

        return json_decode($createResp->getBody()->getContents(), true);
    }
}
