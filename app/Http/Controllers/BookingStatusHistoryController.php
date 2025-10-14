<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingStatusHistoryController extends Controller
{
    public function index($bookingId)
{
    try {
        \Log::info("Attempting to fetch status history for booking: " . $bookingId);
        
        // Check if booking exists first
        $bookingExists = Booking::where('id', $bookingId)->exists();
        
        if (!$bookingExists) {
            \Log::error("Booking not found with ID: " . $bookingId);
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        // Eager load the relationship
        $history = BookingStatusHistory::with('booking')
            ->where('booking_id', $bookingId)
            ->orderBy('changed_at', 'desc')
            ->get();
            
        \Log::info("Successfully retrieved history for booking: " . $bookingId);
        
        return response()->json([
            'success' => true,
            'data' => $history
        ]);
        
    } catch (\Exception $e) {
        \Log::error("Error fetching booking status history: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Server error while retrieving history',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function store(Request $request, $bookingId)
{
    try {
        \Log::info('Creating status history for booking: '.$bookingId, $request->all());
        
        $booking = Booking::findOrFail($bookingId);

        $validated = $request->validate([
            'status' => 'required|string|in:confirmed,checked_in,checked_out,cancelled,no_show',
            'notes' => 'nullable|string|max:500',
        ]);

        $history = $booking->statusHistory()->create([
            'status' => $validated['status'],
            'changed_by' => auth()->user()->name ?? 'system',
            'notes' => $validated['notes'] ?? null,
            'changed_at' => now(),
        ]);

        // Update the booking status
        $booking->update(['booking_status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'data' => $history,
            'message' => 'Status history created successfully'
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Error creating status history: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to create status history',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function show($bookingId, $id)
    {
        $history = BookingStatusHistory::where('booking_id', $bookingId)->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function destroy($bookingId, $id)
    {
        $history = BookingStatusHistory::where('booking_id', $bookingId)->findOrFail($id);
        $history->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Status history deleted successfully'
        ]);
    }
}