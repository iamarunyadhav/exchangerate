<?php

use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\AuthController;
use App\Models\AuditLog;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Mail;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/authenticate', [AuthController::class, 'authenticate']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);

});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'getAuthenticatedUser']);
    Route::middleware('auth:sanctum')->post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});


Route::prefix('exchange-rate')->group(function () {
    Route::get('/current/{baseCurrency?}', [ExchangeRateController::class, 'fetchCurrentRate']);
    Route::get('/{baseCurrency}/last-seven-days', [ExchangeRateController::class, 'getLastSevenDaysRates']);
    Route::get('/recent', [ExchangeRateController::class, 'getRecentRates']);

    Route::middleware('auth:sanctum')->post('/manual', [ExchangeRateController::class, 'storeManualRate']);
});

//email verification
Route::post('/check-user', function (Illuminate\Http\Request $request) {
    $valid = \App\Models\User::where('email', $request->email)->exists();

    return response()->json([
        'access' => $valid,
        'message' => $valid ? 'Access granted' : 'Email not registered',
    ], $valid ? 200 : 401);
});


?>
