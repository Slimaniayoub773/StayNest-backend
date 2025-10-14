<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NewBookingNotification; // Add this
use App\Notifications\BookingConfirmationNotification; // Add this
use App\Notifications\BookingCancellationNotification; // Add this
use App\Models\User;
class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'guest', 'room', 'offer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'guest_id' => 'required|exists:guests,id',
        'room_id' => 'required|exists:rooms,id',
        'offer_id' => 'nullable|exists:offers,id',
        'check_in_date' => 'required|date|after_or_equal:today',
        'check_out_date' => 'required|date|after:check_in_date',
        'booking_status' => 'required|in:pending,confirmed,cancelled,completed',
        'total_price' => 'required|numeric|min:0',
        'payment_status' => 'required|in:unpaid,partial,paid,refunded',
        'number_of_guests' => 'required|integer|min:1',
        'cancellation_policy' => 'nullable|string',
        'special_requests' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $booking = Booking::create($request->all());

    // Send notification to admin about new booking
    $adminUsers = User::whereHas('role', function($query) {
        $query->whereIn('role_name', ['admin', 'manager', 'receptionist']);
    })->get();

    foreach ($adminUsers as $admin) {
        $admin->notify(new NewBookingNotification($booking));
    }

    // If booking is confirmed, send confirmation to user
    if ($booking->booking_status === 'confirmed') {
        $user = User::find($booking->user_id);
        if ($user) {
            $user->notify(new BookingConfirmationNotification($booking));
        }
    }

    return response()->json([
        'success' => true,
        'data' => $booking->load(['user', 'guest', 'room', 'offer'])
    ], 201);
}

    public function show(Booking $booking)
    {
        return response()->json([
            'success' => true,
            'data' => $booking->load(['user', 'guest', 'room', 'offer'])
        ]);
    }

    public function update(Request $request, Booking $booking)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'guest_id' => 'required|exists:guests,id',
        'room_id' => 'required|exists:rooms,id',
        'offer_id' => 'nullable|exists:offers,id',
        'check_in_date' => 'required|date|after_or_equal:today',
        'check_out_date' => 'required|date|after:check_in_date',
        'booking_status' => 'required|in:pending,confirmed,cancelled,completed',
        'total_price' => 'required|numeric|min:0',
        'payment_status' => 'required|in:unpaid,partial,paid,refunded',
        'number_of_guests' => 'required|integer|min:1',
        'cancellation_policy' => 'nullable|string',
        'special_requests' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $oldStatus = $booking->booking_status;
    $booking->update($request->all());

    // Send notification if booking status changed to cancelled
    if ($oldStatus !== 'cancelled' && $booking->booking_status === 'cancelled') {
        $adminUsers = User::whereHas('role', function($query) {
            $query->whereIn('role_name', ['admin', 'manager', 'receptionist']);
        })->get();

        foreach ($adminUsers as $admin) {
            $admin->notify(new BookingCancellationNotification($booking));
        }
    }

    // Send confirmation notification if status changed to confirmed
    if ($oldStatus !== 'confirmed' && $booking->booking_status === 'confirmed') {
        $user = User::find($booking->user_id);
        if ($user) {
            $user->notify(new BookingConfirmationNotification($booking));
        }
    }

    return response()->json([
        'success' => true,
        'data' => $booking->load(['user', 'guest', 'room', 'offer'])
    ]);
}
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully'
        ]);
    }
}