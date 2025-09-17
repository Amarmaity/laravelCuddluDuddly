<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public function home($id)
    {
        $user = Auth::user();
        // $user = User::findOrFail($id); // Load user by ID
        return view('home', compact('user'));
    }


    public function index()
    {
        return view('admin/customer_management/custmerManagement');
    }

    public function registerView()
    {
        return view('customer.registration');
    }


    private function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'mobile'    => 'required|string|max:15|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'usertype'  => 'required|string'
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->mobile,
            'usertype' => $request->usertype,
            'password' => Hash::make($request->password),
        ]);

        return ['user' => $user];
    }

    // 🔹 For Blade/Web
    public function store(Request $request)
    {
        $result = $this->registration($request);

        if (isset($result['errors'])) {
            return back()->withErrors($result['errors'])->withInput();
        }

        return redirect('customer-login')->with('success', 'Registration successful! Welcome ' . $result['user']->name);
    }

    // 🔹 For API
    public function apiRegister(Request $request)
    {
        $result = $this->registration($request);

        if (isset($result['errors'])) {
            return response()->json([
                'status' => 'error',
                'errors' => $result['errors']
            ], 422);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration successful',
            'user'    => $result['user']
        ], 201);
    }


    public function customerLogin()
    {
        return view('customer.login');
    }


    private function authenticate(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string', // email or mobile
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Determine login type
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        // Find user
        $user = User::where($loginType, $request->login)
            ->where('usertype', 'customer') // only customers
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ['errors' => ['login' => ['Invalid credentials or not a customer']]];
        }

        return ['user' => $user];
    }

    // 🔹 For Blade / Web login
    public function loginWeb(Request $request)
    {
        $result = $this->authenticate($request);

        if (isset($result['errors'])) {
            return back()->withErrors($result['errors'])->withInput();
        }

        $user = $result['user'];
        Auth::login($user);

        return redirect()->route('home', ['id' => $user->id]);
    }


    // 🔹 For API login
    public function loginApi(Request $request)
    {
        $result = $this->authenticate($request);

        if (isset($result['errors'])) {
            return response()->json([
                'status' => 'error',
                'errors' => $result['errors']
            ], 422);
        }

        $user = $result['user'];

        // Optional: create API token if using Sanctum or Passport
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'mobile'   => $user->mobile,
                'usertype' => $user->usertype,
            ],
            'token' => $token
        ], 200);
    }

    public function logoutWeb(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    public function logoutApi(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }
}
