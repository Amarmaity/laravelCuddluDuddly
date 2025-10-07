<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;
use App\Models\Sellers;
use Illuminate\Support\Str;
use App\Providers\PayoutService; // we'll create this

class PayoutController extends Controller
{
    public function index()
    {
        $payouts = Payout::with('seller')->latest()->paginate(10);
        return view('admin.payouts.index', compact('payouts'));
    }

    public function create()
    {
        $sellers = Sellers::all();
        return view('admin.payouts.create', compact('sellers'));
    }

    public function store(Request $request, PayoutService $payoutService)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'amount' => 'required|numeric|min:1',
            'mode'   => 'required|string|in:IMPS,NEFT,RTGS,UPI'
        ]);

        $seller = Sellers::findOrFail($request->seller_id);

        if (! $seller->bank_verified) {
            return back()->with('error', 'Seller bank details not verified. Cannot initiate payout.');
        }

        // snapshot
        $beneficiary = [
            'name'    => $seller->bank_name ?? $seller->name,
            'account' => $seller->bank_account_number ?? null,
            'ifsc'    => $seller->ifsc_code ?? null,
            'upi'     => $seller->upi_id ?? null,
        ];

        // 🔹 Mode specific validation
        switch (strtoupper($request->mode)) {
            case 'UPI':
                if (empty($beneficiary['upi'])) {
                    return back()->with('error', 'UPI ID is required for UPI payouts.');
                }
                break;

            case 'IMPS':
            case 'NEFT':
            case 'RTGS':
                if (empty($beneficiary['account']) || empty($beneficiary['ifsc'])) {
                    return back()->with('error', $request->mode . ' requires valid Bank Account and IFSC.');
                }
                break;
        }

        $idempotency = (string) Str::uuid();

        $payout = Payout::create([
            'seller_id'            => $seller->id,
            'initiated_by'         => session('admin_user')->id,
            'amount'               => $request->amount,
            'currency'             => 'INR',
            'method'               => strtoupper($request->mode),
            'provider'             => 'razorpayx',
            'beneficiary_snapshot' => $beneficiary,
            'idempotency_key'      => $idempotency,
            'status'               => 'initiated',
        ]);

        try {
            $resp = $payoutService->initiateRazorpayxPayout($payout);

            $payout->update([
                'provider_payout_id' => $resp['id'] ?? null,
                'provider_response'  => $resp,
                'status'             => in_array($resp['status'], ['queued', 'processing'])
                    ? 'processing'
                    : ($resp['status'] ?? 'pending'),
            ]);

            return redirect()->route('admin.payouts.index')->with('success', 'Payout initiated.');
        } catch (\Exception $e) {
            $payout->update([
                'status' => 'failed',
                'provider_response' => ['error' => $e->getMessage()]
            ]);
            return back()->with('error', 'Failed to initiate payout: ' . $e->getMessage());
        }
    }


    public function edit(Payout $payout)
    {
        $sellers = Sellers::all();
        return view('admin.payouts.edit', compact('payout', 'sellers'));
    }

    public function update(Request $request, Payout $payout)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed',
        ]);

        $payout->update($request->all());

        return redirect()->route('admin.payouts.index')->with('success', 'Payout updated successfully.');
    }

    public function destroy(Payout $payout)
    {
        $payout->delete();
        return back()->with('success', 'Payout deleted.');
    }
}
