<?php

use App\Http\Controllers\GuestRoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Web Routes
Route::get('/payment/{bookingId}', [GuestRoomController::class, 'showPaymentPage']);
Route::get('/receipt/{transactionId}', [GuestRoomController::class, 'getReceipt']);