<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Sellers;
use App\Models\SellerSupport;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SellerSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SellerSupport::with(['seller', 'admin', 'product.primaryImage', 'product.images', 'product.reviews'])
            ->latest();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('seller', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $supports = $query->paginate(15);

        return view('admin.support.sellerSupport', compact('supports'));
    }

    public function searchReview($productId)
    {
        $reviews = Review::where('product_id', $productId)
            ->with(['customer:id,first_name,last_name,email', 'product.primaryImage'])
            ->get()
            ->map(function ($r) {
                $r->customer_name = trim(($r->customer->first_name ?? '') . ' ' . ($r->customer->last_name ?? '')) ?: 'Anonymous';
                $r->customer_email = $r->customer->email ?? 'No email';
                $r->product_image = $r->product?->primaryImage?->image_path;

                $image = $r->product?->primaryImage?->image_path;
                $r->product_image = $image ? "images/products/" . basename($image) : null;
                return $r;
            });

        return response()->json(['reviews' => $reviews]);
    }


    public function show($id)
    {
        $support = SellerSupport::with(['seller', 'admin'])->findOrFail($id);
        return view('admin.support.show', compact('support'));
    }

 
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $support = SellerSupport::findOrFail($id);

        $adminId = auth()->guard('admin')->id();

        switch ($request->status) {
            case 'closed':
                $support->update([
                    'status' => 'closed',
                    'closed_by' => $adminId,
                    'closed_at' => now(),
                ]);
                break;

            case 'reopen':
            case 'reopened':
                $support->update([
                    'status' => 'reopened',
                    'reopened_by' => $adminId,
                    'reopened_at' => now(),
                ]);
                break;

            case 'processing':
            case 'pending':
            case 'open':
                $support->update([
                    'status' => $request->status,
                    'admin_id' => $adminId, // assign current admin
                ]);
                break;
        }

        return response()->json([
            'success' => true,
            'status' => $support->status,
            'closed_by' => $support->closedBy?->name,
            'reopened_by' => $support->reopenedBy?->name,
        ]);
    }


    public function updateBankInfo(Request $request, Sellers $seller)
    {
        $validated = $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:50',
            'upi_id' => 'nullable|string|max:100',
        ]);

        $seller->update($validated);

        return response()->json(['message' => 'Bank info updated successfully.']);
    }


    public function destroy($id)
    {
        SellerSupport::destroy($id);
        return response()->json(['success' => true]);
    }


    public function getMessages($id)
    {
        $messages = SupportMessage::where('seller_support_id', $id)
            ->with(['seller', 'admin'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }


    public function storeMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment.*' => 'nullable|file|max:5120', // allow multiple files
        ]);

        // 🔍 Find related ticket
        $ticket = SellerSupport::findOrFail($id);

        // 📡 Define sender (admin)
        $senderType = 'admin';
        $senderId   = $ticket->admin_id ?? auth()->id();

        if (!$senderId) {
            return response()->json([
                'success' => false,
                'error' => 'No admin assigned to this ticket'
            ], 400);
        }

        // 📎 Handle multiple attachments
        $attachments = [];
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $path = $file->store('support-attachments', 'public');
                $attachments[] = asset('storage/' . $path);
            }
        }

        // 💬 Create the support message
        $message = SupportMessage::create([
            'seller_support_id' => $ticket->id,
            'sender_type'       => $senderType,
            'sender_id'         => $senderId,
            'message'           => $request->message,
            'attachment'        => $attachments ?: null, // stored as JSON automatically
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
