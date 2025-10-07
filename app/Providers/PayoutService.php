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
        // 1) Create Contact (optionally skip if you already maintain contacts)
        $contactResp = $this->client->post('v1/contacts', [
            'json' => [
                'name' => $payout->beneficiary_snapshot['name'],
                'email' => '', // optional,
                'contact' => '', // optional
                'type' => 'vendor'
            ],
            'headers' => [
                'Idempotency-Key' => $payout->idempotency_key // mandatory for payouts per docs
            ]
        ]);

        $contact = json_decode($contactResp->getBody()->getContents(), true);

        // 2) Create Fund Account (bank_account or vpa)
        $fundResp = $this->client->post('v1/fund_accounts', [
            'json' => [
                'contact_id' => $contact['id'],
                'account_type' => 'bank_account',
                'bank_account' => [
                    'name' => $payout->beneficiary_snapshot['name'],
                    'ifsc' => $payout->beneficiary_snapshot['ifsc'],
                    'account_number' => $payout->beneficiary_snapshot['account']
                ]
            ],
            'headers' => ['Idempotency-Key' => $payout->idempotency_key]
        ]);
        $fund = json_decode($fundResp->getBody()->getContents(), true);

        // 3) Create payout
        $createResp = $this->client->post('v1/payouts', [
            'json' => [
                'account_number' => $payout->beneficiary_snapshot['account'], // provider-specific
                'fund_account_id' => $fund['id'],
                'amount' => intval($payout->amount * 100), // paise if required
                'currency' => $payout->currency,
                'mode' => 'IMPS', // or NEFT/RTGS/UPI depending on beneficiary
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
