<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {  
        $request->validate([
            'email_or_phone' => 'required',
            'password' => 'required',
            'user_type' => 'required',
        ]);

        $admin = AdminUser::where('email', $request->email_or_phone)
            ->orWhere('phone', $request->email_or_phone)
            ->first();

        if (!$admin) {
            return back()->withErrors(['email_or_phone' => 'No account found with this email or phone.'])
                ->withInput();
        }

        if (!Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])
                ->withInput();
        }

        if ($admin->role !== $request->user_type) {
            return back()->withErrors(['user_type' => 'User type does not match.'])
                ->withInput();
        }

        if (!$admin->is_active == 1) {
            return back()->withErrors(['status' => 'Your account is inactive. Please contact admin.'])
                ->withInput();
        }

        session(['admin_user' => $admin]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Welcome back, ' . $admin->name . '!');
    }



    public function logout()
    {
        session()->forget('admin_user');
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }
}