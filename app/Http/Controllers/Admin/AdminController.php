<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function showLoginForm()
    {
        return view('admin.login');
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email_or_phone' => 'required|string',
    //         'password' => 'required|string',
    //         'user_type' => 'required|string',
    //     ]);

    //     $emailOrPhone = $request->input('email_or_phone');
    //     $password = $request->input('password');

    //     // Try email first
    //     $credentials = ['email' => $emailOrPhone, 'password' => $password];
    //     if (Auth::guard('admin')->attempt($credentials)) {
    //         $request->session()->regenerate();
    //         return redirect()->route('admin.dashboard');
    //     }

    //     // Try phone if not email
    //     $credentials = ['phone' => $emailOrPhone, 'password' => $password];
    //     if (Auth::guard('admin')->attempt($credentials)) {
    //         $request->session()->regenerate();
    //         return redirect()->route('admin.dashboard');
    //     }

    //     return back()->withErrors([
    //         'login_error' => 'Invalid email, phone, or password.',
    //     ])->withInput();
    // }


    public function login(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required|string',
            'password' => 'required|string',
            'user_type' => 'required|string',
        ]);

        $emailOrPhone = $request->email_or_phone;
        $password = $request->password;
        $role = $request->user_type;

        $admin = AdminUser::where('email', $emailOrPhone)
            ->orWhere('phone', $emailOrPhone)
            ->first();

        if (!$admin) {
            return back()
                ->withErrors(['email_or_phone' => 'No account found with this email or phone.'])
                ->withInput();
        }

        if (!Hash::check($password, $admin->password)) {
            return back()
                ->withErrors(['password' => 'Incorrect password.'])
                ->withInput();
        }

        if ($admin->role !== $role) {
            return back()
                ->withErrors(['user_type' => 'User type does not match your account.'])
                ->withInput();
        }

        if (!$admin->is_active) {
            return back()
                ->withErrors(['status' => 'Your account is inactive. Please contact the administrator.'])
                ->withInput();
        }

        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Welcome back, ' . $admin->name . '!');
    }


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
