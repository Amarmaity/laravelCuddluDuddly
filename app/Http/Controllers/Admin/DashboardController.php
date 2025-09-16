<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login')->withErrors(['login_error' => 'Please login first']);
        }
        return view('admin.dashboard');
    }
}
