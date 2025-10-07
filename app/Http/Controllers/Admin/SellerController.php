<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sellers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\SellerKycStatusMail;

class SellerController extends Controller
{

    public function AllSellers()
    {
        echo 'under develop';
    }

    public function index(Request $request)
    {
        $query = Sellers::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('postal_code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        switch ($request->get('sort')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('name');
                break;
            case 'products':
                $query->withCount('products')->orderBy('products_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $sellers = $query->where('compliance_status', 'verified')
            ->paginate(20)
            ->withQueryString();
        return view('admin.sellers.index', compact('sellers'));
    }

    public function create()
    {
        return view('admin.sellers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'contact_person'    => 'required|string|max:255',
            'email'             => 'required|email|unique:sellers,email',
            'phone'             => 'required|string|max:20|unique:sellers,phone',
            'address'           => 'nullable|string|max:500',
            'city'              => 'nullable|string|max:255',
            'state'             => 'nullable|string|max:255',
            'country'           => 'nullable|string|max:255',
            'postal_code'       => 'nullable|string|max:20',
            // 'gst_number'        =>  ['nullable', new \App\Rules\ValidGSTIN],
            // 'pan_number'        =>  ['nullable', new \App\Rules\ValidPAN],
            'gst_number'           => 'nullable|string|max:50',
            'pan_number'       => 'nullable|string|max:20',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name'         => 'nullable|string|max:255',
            'ifsc_code'         => 'nullable|string|max:50',
            'upi_id'     => 'nullable|string|max:100',
            'commission_rate'   => 'nullable|numeric|min:0|max:100',
            'logo'              => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'documents.*'       => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
            'is_active'         => 'nullable|boolean',
        ]);

        $data = $request->except(['logo', 'documents', 'is_active']);
        // var_dump($data);exit;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('seller_docs', 'public');
        }

        if ($request->hasFile('documents')) {
            $documentPaths = [];
            foreach ($request->file('documents') as $doc) {
                $documentPaths[] = $doc->store('seller_docs', 'public');
            }
            $data['documents'] = json_encode($documentPaths);
        }

        $data['compliance_status'] = 'pending';

        Sellers::create($data);

        return redirect()->route('admin.sellers.index')
            ->with('success', 'Seller created successfully.');
    }

    public function show(Sellers $seller)
    {
        return view('admin.sellers.show', compact('seller'));
    }

    public function edit(Sellers $seller)
    {
        return view('admin.sellers.edit', compact('seller'));
    }

    public function update(Request $request, Sellers $seller)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:sellers,email,' . $seller->id,
            'phone' => 'required|string|max:20|unique:sellers,phone,' . $seller->id,
        ]);

        $seller->update($request->all());

        return redirect()->route('admin.sellers.index')
            ->with('success', 'Seller updated successfully');
    }

    public function destroy(Sellers $seller)
    {
        $seller->delete();

        return redirect()->route('admin.sellers.index')
            ->with('success', 'Seller deleted successfully');
    }



    public function viewDocs(Sellers $seller)
    {
        $docs = json_decode($seller->documents, true);

        if (empty($docs) || !is_array($docs)) {
            return back()->with('error', 'Documents not found for this seller.');
        }

        $filePath = $docs[0];

        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'Documents not found for this seller.');
        }

        $fullPath = Storage::disk('public')->path($filePath);

        return response()->file($fullPath);
    }


    public function compliance()
    {
        // Pending applications
        $pendingKyc = Sellers::where('compliance_status', 'pending')->paginate(20, ['*'], 'pending_page');

        // Rejected applications
        $rejectedKyc = Sellers::where('compliance_status', 'rejected')
            ->paginate(20, ['*'], 'rejected_page');

        return view('admin.sellers.compliance', compact('pendingKyc', 'rejectedKyc'));
    }

    public function KYCaccept($id)
    {
        $seller = Sellers::findOrFail($id);

        $seller->update([
            'compliance_status' => 'verified',
        ]);

        Mail::to($seller->email)->send(new SellerKycStatusMail($seller, 'verified'));

        return back()->with('success', 'Seller KYC verified successfully and email sent.');
    }

    public function KYCreject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $seller = Sellers::findOrFail($id);

        $seller->update([
            'compliance_status' => 'rejected',
        ]);

        Mail::to($seller->email)->send(new SellerKycStatusMail(
            $seller,
            'rejected',
            $request->rejection_reason
        ));

        return back()->with('success', 'Seller KYC rejected successfully and email sent.');
    }

    public function bankDetails(Sellers $seller)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $seller->bank_holder_name ?? $seller->name,
                'account' => $seller->bank_account_number,
                'ifsc' => $seller->ifsc_code,
                'upi' => $seller->upi_id,
                'verified' => (bool) $seller->bank_verified
            ]
        ]);
    }
}
