<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ValidationController;

Route::prefix('validate')->group(function () {
    Route::get('/gst/{gst}', [ValidationController::class, 'validateGST']);
    Route::get('/pan/{pan}', [ValidationController::class, 'validatePAN']);
});
