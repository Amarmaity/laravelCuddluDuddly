<?php

use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [CustomerController::class, 'apiRegister']);
Route::post('/loginapi', [CustomerController::class, 'loginApi']);
Route::post('/logout',[CustomerController::class, 'logoutApi']);
