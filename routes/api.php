<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\User\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/register', RegisterController::class);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('owner/properties', [PropertyController::class, 'index']);
    Route::post('owner/properties', [PropertyController::class, 'store']);
    Route::get('user/bookings', [BookingController::class, 'index']);
});
