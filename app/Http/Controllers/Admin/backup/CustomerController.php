<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class CustomerController extends Controller
{

    // public function home($id)
    // {
    //     $user = Auth::user();
    //     // $user = User::findOrFail($id); // Load user by ID
    //     return view('home', compact('user'));
    // }


    // public function index()
    // {
    //     return view('admin/customer_management/custmerManagement');
    // }

    // public function registerView()
    // {
    //     return view('customer.registration');
    // }


    // private function registration(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'first_name' => 'required|string|max:255',
    //         'last_name'  => 'required|string|max:255',
    //         'email'      => 'required|string|email|max:255|unique:users',
    //         'dob'        => 'required|date',
    //         'gender'     => 'required|in:male,female,other',
    //         'phone'     => 'required|string|max:15|unique:users',
    //         'password'   => 'required|string|min:6|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return ['errors' => $validator->errors()];
    //     }

    //     $user = User::create([
    //         'first_name' => $request->first_name,
    //         'last_name' => $request->last_name,
    //         'email'    => $request->email,
    //         'phone'   => $request->phone,
    //         'dob'      => $request->dob,
    //         'gender'   => $request->gender,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return ['user' => $user];
    // }


    // // 🔹 For Blade/Web
    // public function store(Request $request)
    // {
    //     $result = $this->registration($request);

    //     if (isset($result['errors'])) {
    //         return back()->withErrors($result['errors'])->withInput();
    //     }

    //     return redirect('customer-login')->with('success', 'Registration successful! Welcome ' . $result['user']->name);
    // }

    // // 🔹 For API
    // public function apiRegister(Request $request)
    // {
    //     $result = $this->registration($request);

    //     if (isset($result['errors'])) {
    //         return response()->json([
    //             'status' => 'error',
    //             'errors' => $result['errors']
    //         ], 422);
    //     }

    //     return response()->json([
    //         'status'  => 'success',
    //         'message' => 'Registration successful',
    //         'user'    => $result['user']
    //     ], 201);
    // }


    // public function customerLogin()
    // {
    //     return view('customer.login');
    // }


    // private function authenticate(Request $request)
    // {
    //     // Validate input
    //     $validator = Validator::make($request->all(), [
    //         'login'    => 'required|string', // email or mobile
    //         'password' => 'required|string|min:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return ['errors' => $validator->errors()];
    //     }

    //     // Determine login type
    //     $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

    //     // Find user
    //     $user = User::where($loginType, $request->login)
    //         ->where('usertype', 'customer') // only customers
    //         ->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return ['errors' => ['login' => ['Invalid credentials or not a customer']]];
    //     }

    //     return ['user' => $user];
    // }

    // // 🔹 For Blade / Web login
    // public function loginWeb(Request $request)
    // {
    //     $result = $this->authenticate($request);

    //     if (isset($result['errors'])) {
    //         return back()->withErrors($result['errors'])->withInput();
    //     }

    //     $user = $result['user'];
    //     Auth::login($user);

    //     return redirect()->route('home', ['id' => $user->id]);
    // }


    // // 🔹 For API login
    // public function loginApi(Request $request)
    // {
    //     $result = $this->authenticate($request);

    //     if (isset($result['errors'])) {
    //         return response()->json([
    //             'status' => 'error',
    //             'errors' => $result['errors']
    //         ], 422);
    //     }

    //     $user = $result['user'];

    //     // Optional: create API token if using Sanctum or Passport
    //     $token = $user->createToken('API Token')->plainTextToken;

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Login successful',
    //         'user' => [
    //             'id'       => $user->id,
    //             'name'     => $user->name,
    //             'email'    => $user->email,
    //             'mobile'   => $user->mobile,
    //             'usertype' => $user->usertype,
    //         ],
    //         'token' => $token
    //     ], 200);
    // }

    // public function logoutWeb(Request $request)
    // {
    //     Auth::logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return redirect('/')->with('success', 'You have been logged out successfully.');
    // }

    // public function logoutApi(Request $request)
    // {
    //     $user = $request->user();
    //     $user->tokens()->delete();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Logged out successfully'
    //     ], 200);
    // }

    // public function 



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
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'gender'      => 'required|in:male,female,other',
            'email'       => 'required|email|unique:users,email,' . $customer->id,
            'phone'       => 'nullable|string|max:20',
            'dob'         => 'required|date',
        ]);

        $customer->update($request->only(['first_name', 'last_name', 'email', 'phone', 'gender', 'dob']));

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
        echo 'hi';
        exit;
        $ids = $request->ids ?? [];

        if (!empty($ids)) {
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
