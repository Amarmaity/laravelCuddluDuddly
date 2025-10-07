<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payout;

class WebhookController extends Controller
{
    public function razorpayx(Request $request)
    {
        $signature = $request->header('X-Razorpay-Signature') ?? $request->header('x-razorpay-signature');
        $body = $request->getContent();
        $secret = config('services.razorpay.webhook_secret');

        $expected = hash_hmac('sha256', $body, $secret);

        if (!hash_equals($expected, $signature)) {
            Log::warning('Invalid razorpay webhook signature');
            return response('invalid signature', 401);
        }

        $payload = $request->json()->all();
        // example: $payload['payload']['payout']['entity']['id'] ...
        $event = data_get($payload, 'event');

        if ($event === 'payout.processed' || $event === 'payout.failed' || $event === 'payout.created') {
            $providerId = data_get($payload, 'payload.payout.entity.id');
            $status = data_get($payload, 'payload.payout.entity.status');
            $payout = Payout::where('provider_payout_id', $providerId)->first();

            if ($payout) {
                $payout->provider_response = array_merge($payout->provider_response ?? [], $payload);
                $payout->status = $this->mapProviderStatusToLocal($status);
                if ($payout->status === 'completed') $payout->processed_at = now();
                $payout->save();
            }
        }

        return response('ok', 200);
    }

    protected function mapProviderStatusToLocal($status)
    {
        return match ($status) {
            'processed', 'success' => 'completed',
            'failed' => 'failed',
            'queued', 'created' => 'processing',
            default => 'pending'
        };
    }
}
