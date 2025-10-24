<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Returns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    /**
     * Display all returns.
     */
    public function index(Request $request)
    {
        $query = Returns::with(['order', 'orderItem', 'user'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', fn($q) =>
            $q->where('order_number', 'like', "%{$search}%"))
                ->orWhere('return_number', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $returns = $query->paginate(20);

        return view('admin.returns.index', compact('returns'));
    }


    /**
     * Show the form for creating a new return.
     */
    public function create()
    {
        $orders = Order::with(['user', 'items.product'])
            ->whereIn('order_status', ['delivered', 'shipped']) // only eligible for return
            ->latest()
            ->take(100)
            ->get();

        return view('admin.returns.create', compact('orders'));
    }


    /**
     * Store a newly created return.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'order_item_id'  => 'nullable|exists:order_items,id',
            'reason'         => 'required|string|max:1000',
            'refund_method'  => 'nullable|string|max:50',
            'refund_amount'  => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // ✅ Automatically get user_id from the order
            $order = Order::with('user')->findOrFail($request->order_id);
            $userId = $order->user_id;

            // Generate return number using that user
            $returnNumber = Returns::generateReturnNumber($userId);

            // Create return record
            Returns::create([
                'return_number' => $returnNumber,
                'order_id'      => $order->id,
                'order_item_id' => $request->order_item_id,
                'user_id'       => $userId,
                'reason'        => $request->reason,
                'status'        => 'requested',
                'refund_method' => $request->refund_method,
                'refund_amount' => $request->refund_amount,
            ]);

            DB::commit();
            return redirect()
                ->route('admin.returns.index')
                ->with('success', 'Return request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create return: ' . $e->getMessage()]);
        }
    }


    /**
     * Show a single return details.
     */
    public function show($id)
    {
        $return = Returns::with(['order', 'orderItem', 'user'])->findOrFail($id);
        return view('admin.returns.show', compact('return'));
    }

    /**
     * Update return status (approve/reject/received/refunded)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:requested,approved,rejected,received,refunded',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $return = Returns::findOrFail($id);
        $return->update($validated);

        return redirect()
            ->route('admin.returns.index')
            ->with('success', 'Return updated successfully.');
    }

    /**
     * Delete a return record.
     */
    public function destroy($id)
    {
        $return = Returns::findOrFail($id);
        $return->delete();

        return redirect()
            ->route('admin.returns.index')
            ->with('success', 'Return deleted successfully.');
    }
}
