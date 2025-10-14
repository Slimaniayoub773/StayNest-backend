<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PaymentNotification; // Add this
use App\Models\User;
class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['booking.room', 'booking.guest'])
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'booking_id' => 'required|exists:bookings,id',
        'payment_method' => 'required|string|in:cash,credit_card,debit_card,bank_transfer,other',
        'amount' => 'required|numeric|min:0.01',
        'status' => 'required|string|in:pending,completed,failed,refunded,partially_refunded',
        'receipt_url' => 'nullable|url',
        'transaction_id' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $payment = Payment::create([
        'booking_id' => $request->booking_id,
        'payment_method' => $request->payment_method,
        'amount' => $request->amount,
        'status' => $request->status,
        'receipt_url' => $request->receipt_url,
        'transaction_id' => $request->transaction_id,
        'payment_date' => now()
    ]);

    // Update booking payment status if needed
    $this->updateBookingPaymentStatus($payment->booking);

    // Send payment notification to admin
    $adminUsers = User::whereHas('role', function($query) {
        $query->whereIn('role_name', ['admin', 'manager', 'receptionist']);
    })->get();

    foreach ($adminUsers as $admin) {
        $admin->notify(new PaymentNotification($payment));
    }

    return response()->json([
        'success' => true,
        'data' => $payment->load('booking.room', 'booking.guest')
    ], 201);
}
    public function show(Payment $payment)
    {
        return response()->json([
            'success' => true,
            'data' => $payment->load('booking.room', 'booking.guest')
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string|in:cash,credit_card,debit_card,bank_transfer,other',
            'amount' => 'required|numeric|min:0.01',
            'status' => 'required|string|in:pending,completed,failed,refunded,partially_refunded',
            'receipt_url' => 'nullable|url',
            'transaction_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payment->update([
            'booking_id' => $request->booking_id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'status' => $request->status,
            'receipt_url' => $request->receipt_url,
            'transaction_id' => $request->transaction_id
        ]);

        // Update booking payment status if needed
        $this->updateBookingPaymentStatus($payment->booking);

        return response()->json([
            'success' => true,
            'data' => $payment->load('booking.room', 'booking.guest')
        ]);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        // Update booking payment status if needed
        $this->updateBookingPaymentStatus($payment->booking);

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ]);
    }

    protected function updateBookingPaymentStatus(Booking $booking)
    {
        $totalPaid = $booking->payments()->where('status', 'completed')->sum('amount');
        $totalRefunded = $booking->payments()->where('status', 'refunded')->sum('amount');
        $netPaid = $totalPaid - $totalRefunded;

        if ($netPaid <= 0) {
            $booking->update(['payment_status' => 'unpaid']);
        } elseif ($netPaid >= $booking->total_price) {
            $booking->update(['payment_status' => 'paid']);
        } else {
            $booking->update(['payment_status' => 'partial']);
        }
    }

    public function getPaymentsByBooking($bookingId)
    {
        $payments = Payment::where('booking_id', $bookingId)
            ->with(['booking.room', 'booking.guest'])
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }
}