<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cancellation;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CancellationController extends Controller
{
    public function index(Request $request)
    {
        $query = Cancellation::with(['order', 'user']);

        // 🔍 Search filter (by order no or cancellation id)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        //  Order and paginate
        $cancellations = $query->orderBy('created_at', 'desc')->paginate(15);

        //  Preserve filters in pagination links
        $cancellations->appends($request->all());

        return view('admin.cancellations.index', compact('cancellations'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason'   => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->order_status === 'cancelled') {
            return response()->json(['message' => 'Order is already cancelled.'], 400);
        }

        Cancellation::create([
            'order_id'    => $order->id,
            'user_id'     => $order->user_id,
            'reason'      => $request->reason ?? 'No reason provided.',
            'status'      => 'pending',
        ]);

        return response()->json(['message' => 'Order cancellation request successful.']);
    }


    /**
     * Show the details of a single cancellation.
     */
    public function show($id)
    {
        $cancellation = Cancellation::with(['order', 'user'])->findOrFail($id);

        return view('admin.cancellations.show', compact('cancellation'));
    }

    
    public function approve($id)
    {
        $cancellation = Cancellation::findOrFail($id);
        $order = $cancellation->order;
        $cancellation->update([
            'status' => 'approved',
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
        ]);

        // Update order status
        $order->update(['order_status' => 'cancelled']);
        
        // Optional: restore stock, issue refund logic here
        // $this->restoreStock($order);
        // $this->initiateRefund($order);

        return redirect()->back()->with('success', 'Cancellation approved successfully.');
    }

    /**
     * Reject a cancellation request.
     */
    public function reject($id)
    {
        $cancellation = Cancellation::findOrFail($id);

        $cancellation->update([
            'status' => 'rejected',
            'approved_by' => Auth::guard('admin')->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Cancellation rejected successfully.');
    }

    
    public function destroy($id)
    {
        $cancellation = Cancellation::findOrFail($id);
        $cancellation->delete();

        return redirect()->back()->with('success', 'Cancellation record deleted successfully.');
    }

    /**
     * Optional: Restore stock after cancellation
     */
    protected function restoreStock($order)
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->increment('stock', $item->quantity);
        }
    }

    /**
     * Optional: Refund logic (stub for now)
     */
    protected function initiateRefund($order)
    {
        // Integrate with payment gateway refund API here
        // Example: Razorpay, Stripe, etc.
    }
}
