<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();

        // 🔍 Search by name, email or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // 🟢 Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ↕️ Sorting
        switch ($request->get('sort')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('first_name')->orderBy('last_name');
                break;
            case 'orders':
                // Assuming you have relation `orders()` in User model
                $query->withCount('orders')->orderBy('orders_count', 'desc');
                break;
            default: // latest
                $query->latest();
        }

        $customers = $query->paginate(10)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }
    // Show create form
    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|string|max:20',
            'dob'        => 'nullable|date|before:today',
            'gender'     => 'nullable|in:male,female,other',
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'dob',
            'gender',
        ]);

        $data['password'] = bcrypt($request->password ?? str()->random(8));
        $data['status']   = 'active';

        User::create($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }



    // Show customer details
    public function show(User $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    // Edit form
    public function edit(User $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    // Update
    public function update(Request $request, User $customer)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $customer->update($request->only(['name', 'email', 'phone']));

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    // public function destroy(Request $request, $id = null)
    // {
    //     try {
    //         if ($id) {
    //             // 🔹 Single delete
    //             $customer = User::findOrFail($id);
    //             $customer->delete();
    //         } elseif ($request->has('ids')) {
    //             // 🔹 Bulk delete
    //             $ids = $request->input('ids', []);
    //             User::whereIn('id', $ids)->delete();
    //         }

    //         return redirect()
    //             ->route('admin.customers.index')
    //             ->with('success', 'Customer(s) deleted successfully.');
    //     } catch (\Exception $e) {
    //         return redirect()
    //             ->route('admin.customers.index')
    //             ->with('error', 'Failed to delete customer(s): ' . $e->getMessage());
    //     }
    // }



    public function toggleStatus(User $customer)
    {
        $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->save();

        return response()->json([
            'success' => true,
            'status'  => $customer->status,
            'message' => 'Status updated successfully!'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        // decode if it comes as JSON string
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        if (!empty($ids) && is_array($ids)) {
            User::whereIn('id', $ids)->delete();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer(s) deleted successfully.'
            ]);
        }

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer(s) deleted successfully.');
    }
}
