<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class GuestProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        try {
            $guest = $request->user();
            
            // Get bookings with related data
            $bookings = $guest->bookings()
                ->with(['room', 'room.type', 'payments'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'guest' => $guest,
                'bookings' => $bookings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to fetch profile data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $guest = $request->user();
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:guests,email,' . $guest->id,
                'phone' => 'required|string|max:20',
                'identification_number' => 'required|string|max:50|unique:guests,identification_number,' . $guest->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $guest->update($request->only(['name', 'email', 'phone', 'identification_number']));

            return response()->json([
                'message' => 'Profile updated successfully',
                'guest' => $guest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $guest = $request->user();
            
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Hash::check($request->current_password, $guest->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect'
                ], 401);
            }

            $guest->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'message' => 'Password updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Password update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getBookingReviews($bookingId)
    {
        try {
            $guest = Auth::guard('guest')->user();
            
            if (!$guest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            // Verify the booking belongs to the authenticated guest
            $booking = Booking::where('id', $bookingId)
                ->where('guest_id', $guest->id)
                ->first();
                
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or access denied'
                ], 404);
            }
            
            $reviews = Review::where('booking_id', $bookingId)
                ->with('guest')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                        'guest_name' => $review->guest->name
                    ];
                });
                
            return response()->json([
                'success' => true,
                'reviews' => $reviews
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reviews: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit a review for a booking
     */
    public function submitReview(Request $request, $bookingId)
    {
        try {
            $guest = Auth::guard('guest')->user();
            
            if (!$guest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Verify the booking belongs to the authenticated guest
            $booking = Booking::where('id', $bookingId)
                ->where('guest_id', $guest->id)
                ->first();
                
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or access denied'
                ], 404);
            }
            
            // Check if booking is eligible for review (confirmed and within stay dates)
            $today = now();
            if ($booking->booking_status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only confirmed bookings can be reviewed'
                ], 400);
            }
            
            if ($today < $booking->check_in_date || $today > $booking->check_out_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only review during your stay (between check-in and check-out dates)'
                ], 400);
            }
            
            // Check if guest already reviewed this booking
            $existingReview = Review::where('booking_id', $bookingId)
                ->where('guest_id', $guest->id)
                ->first();
                
            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this booking'
                ], 409);
            }
            
            // Create the review
            $review = Review::create([
                'guest_id' => $guest->id,
                'room_id' => $booking->room_id,
                'booking_id' => $bookingId,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
            
            // Load guest relationship for response
            $review->load('guest');
            
            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'review' => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                    'guest_name' => $review->guest->name
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a booking is eligible for review
     */
    public function checkReviewEligibility($bookingId)
    {
        try {
            $guest = Auth::guard('guest')->user();
            
            if (!$guest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            // Verify the booking belongs to the authenticated guest
            $booking = Booking::where('id', $bookingId)
                ->where('guest_id', $guest->id)
                ->first();
                
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or access denied'
                ], 404);
            }
            
            // Check if booking is eligible for review
            $today = now();
            $isEligible = ($booking->booking_status === 'confirmed') &&
                         ($today >= $booking->check_in_date) &&
                         ($today <= $booking->check_out_date);
            
            // Check if guest already reviewed this booking
            $hasReviewed = Review::where('booking_id', $bookingId)
                ->where('guest_id', $guest->id)
                ->exists();
            
            return response()->json([
                'success' => true,
                'eligible' => $isEligible && !$hasReviewed,
                'has_reviewed' => $hasReviewed,
                'booking_status' => $booking->booking_status,
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $booking->check_out_date,
                'current_date' => $today->format('Y-m-d')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check review eligibility: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reviews for a room (public endpoint)
     */
    public function getRoomReviews($roomId)
    {
        try {
            $reviews = Review::with('guest')
                ->where('room_id', $roomId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                        'guest_name' => $review->guest->name
                    ];
                });
                
            return response()->json([
                'success' => true,
                'reviews' => $reviews
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch room reviews: ' . $e->getMessage()
            ], 500);
        }
    }

}