<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RoomImage;
use App\Models\RoomAmenity;
use App\Models\RoomOffer;
use App\Models\Offer;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Review;
use App\Models\CleaningSchedule;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class GuestRoomController extends Controller
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all available rooms with their details
     */
    public function getRooms(Request $request)
{
    try {
        // Get query parameters for filtering
        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');
        $roomType = $request->query('room_type');
        $maxOccupancy = $request->query('max_occupancy');
        $limit = $request->query('limit');
        
        // Start building the query
        $query = Room::with([
            'type',
            'images',
            'amenities',
            'offers' => function($q) use ($checkIn, $checkOut) {
                $q->where('is_active', true);
                // Apply date filters based on the context
                if ($checkIn && $checkOut) {
                    $q->where('start_date', '<=', $checkIn)
                      ->where('end_date', '>=', $checkOut);
                } else {
                    // Default to current active offers
                    $q->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
                }
            },
            'roomOffers.offer',
            'reviews'
        ])->where('status', 'available');
        
        // Add average rating and reviews count
        $query->withAvg('reviews', 'rating')
              ->withCount('reviews');
        
        // Filter by room type if provided
        if ($roomType) {
            $query->whereHas('type', function($q) use ($roomType) {
                $q->where('name', 'like', '%' . $roomType . '%');
            });
        }
        
        if ($maxOccupancy) {
            $query->whereHas('type', function($q) use ($maxOccupancy) {
                $q->where('max_occupancy', '>=', $maxOccupancy);
            });
        }
        
        // Check availability for dates if provided
        if ($checkIn && $checkOut) {
            $query->whereDoesntHave('bookings', function($q) use ($checkIn, $checkOut) {
                $q->where(function($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                          ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                          ->orWhere(function($q) use ($checkIn, $checkOut) {
                              $q->where('check_in_date', '<=', $checkIn)
                                ->where('check_out_date', '>=', $checkOut);
                          });
                })->whereIn('booking_status', ['confirmed', 'pending']);
            });
        }
        
        // Apply limit if provided
        if ($limit) {
            $query->limit($limit);
        }
        
        $rooms = $query->get()->map(function($room) {
            return $this->formatRoomData($room);
        });
        
        return response()->json([
            'success' => true,
            'data' => $rooms,
            'message' => 'Rooms retrieved successfully'
        ]);
        
    } catch (\Exception $e) {
        // Notify system error
       
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve rooms: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Get a specific room with all details
     */
    public function getRoomDetails($id)
{
    try {
        $room = Room::with([
            'type',
            'images',
            'amenities',
            'offers' => function($q) {
                $q->where('is_active', true)
                  ->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
            }
        ])->findOrFail($id);
        
        $formattedRoom = $this->formatRoomData($room);
        
        return response()->json([
            'success' => true,
            'data' => $formattedRoom,
            'message' => 'Room details retrieved successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Room not found: ' . $e->getMessage()
        ], 404);
    }
}
    /**
     * Check room availability for specific dates
     */
    public function checkAvailability(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'check_in' => 'required|date|after:today',
                'check_out' => 'required|date|after:check_in',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $room = Room::findOrFail($id);
            
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;
            
            $isAvailable = !$room->bookings()
                ->where(function($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                          ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                          ->orWhere(function($q) use ($checkIn, $checkOut) {
                              $q->where('check_in_date', '<=', $checkIn)
                                ->where('check_out_date', '>=', $checkOut);
                          });
                })
                ->whereIn('booking_status', ['confirmed', 'pending'])
                ->exists();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'available' => $isAvailable,
                    'room_id' => $id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut
                ],
                'message' => $isAvailable ? 'Room is available' : 'Room is not available for the selected dates'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking form data for a specific room
     */
    public function getBookingFormData(Request $request, $id)
{
    try {
        $room = Room::with(['type', 'offers'])->findOrFail($id);

        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');

        // Get offers valid for the specific dates
        $offers = $room->offers()
            ->where('is_active', true)
            ->when($checkIn && $checkOut, function($query) use ($checkIn, $checkOut) {
                return $query->where('start_date', '<=', $checkIn)
                           ->where('end_date', '>=', $checkOut);
            }, function($query) {
                // Default to currently active offers if no dates provided
                return $query->where('start_date', '<=', now())
                           ->where('end_date', '>=', now());
            })
            ->get()
            ->map(function($offer) {
                return [
                    'id' => $offer->id,
                    'title' => $offer->title,
                    'discount_percentage' => $offer->discount_percentage,
                    'promo_code' => $offer->promo_code,
                    'description' => $offer->description
                ];
            });

        $formData = [
            'room_id' => $room->id,
            'room_number' => $room->room_number,
            'room_type' => $room->type ? $room->type->name : null,
            'base_price' => $room->price_per_night,
            'max_occupancy' => $room->type ? $room->type->max_occupancy : null,
            'available_offers' => $offers,
            'cancellation_policy' => "Free cancellation up to 24 hours before check-in."
        ];

        return response()->json([
            'success' => true,
            'data' => $formData,
            'message' => 'Booking form data retrieved successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving booking form data: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Process booking request with notifications
     */
    public function createBooking(Request $request)
{
    try {
        // 1️⃣ Data validation
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'guest_info' => 'required|array',
            'guest_info.name' => 'required|string',
            'guest_info.email' => 'required|email',
            'guest_info.phone' => 'required|string',
            'guest_info.identification_number' => 'nullable|string',
            'special_requests' => 'nullable|string',
            'offer_id' => 'nullable|exists:offers,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2️⃣ Check room availability
        $room = Room::findOrFail($request->room_id);
        $isAvailable = !$room->bookings()
            ->where(function($query) use ($request) {
                $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('check_in_date', '<=', $request->check_in_date)
                            ->where('check_out_date', '>=', $request->check_out_date);
                      });
            })
            ->whereIn('booking_status', ['confirmed', 'pending'])
            ->exists();

        if (!$isAvailable) {
            // Notify about booking conflict
        
            
            return response()->json([
                'success' => false,
                'message' => 'Room is no longer available for the selected dates'
            ], 409);
        }

        // 3️⃣ Check guest capacity
        if ($request->number_of_guests > $room->type->max_occupancy) {
            return response()->json([
                'success' => false,
                'message' => 'Number of guests exceeds room capacity'
            ], 422);
        }

        // 4️⃣ Create or find guest
        $guest = Guest::firstOrCreate(
            ['email' => $request->guest_info['email']],
            [
                'name' => $request->guest_info['name'],
                'phone' => $request->guest_info['phone'],
                'identification_number' => $request->guest_info['identification_number'] ?? null
            ]
        );

        // 5️⃣ Calculate base price
        $checkIn = new \DateTime($request->check_in_date);
        $checkOut = new \DateTime($request->check_out_date);
        $nights = $checkIn->diff($checkOut)->days;
        $basePrice = $room->price_per_night * $nights;

        $discount = 0;
        $appliedOfferId = null;

        // 6️⃣ Apply valid offer - IMPROVED FLEXIBLE VERSION
        if ($request->offer_id) {
            \Log::info('=== OFFER VALIDATION (IMPROVED) ===');
            
            $offer = Offer::find($request->offer_id);
            
            if (!$offer) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected offer was not found'
                ], 422);
            }

            if (!$offer->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected offer is not active'
                ], 422);
            }

            // IMPROVED: More flexible date validation
            $checkInDate = Carbon::parse($request->check_in_date);
            $offerStart = Carbon::parse($offer->start_date);
            $offerEnd = Carbon::parse($offer->end_date);
            
            // Check if ANY part of the stay overlaps with the offer period
            $checkOutDate = Carbon::parse($request->check_out_date);
            
            $isDateValid = $checkInDate->lte($offerEnd) && $checkOutDate->gte($offerStart);
            
            \Log::info('Improved date validation:', [
                'check_in' => $request->check_in_date,
                'check_out' => $request->check_out_date,
                'offer_start' => $offer->start_date,
                'offer_end' => $offer->end_date,
                'is_valid' => $isDateValid
            ]);

            if (!$isDateValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected offer is not valid for your stay dates'
                ], 422);
            }

            // Check room-offer link
            $isLinkedToRoom = DB::table('room_offers')
                ->where('room_id', $room->id)
                ->where('offer_id', $offer->id)
                ->exists();

            if (!$isLinkedToRoom) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected offer is not available for this room'
                ], 422);
            }

            $discount = ($basePrice * $offer->discount_percentage) / 100;
            $appliedOfferId = $offer->id;
            
            \Log::info('✅ Offer applied successfully', [
                'base_price' => $basePrice,
                'discount_percentage' => $offer->discount_percentage,
                'discount_amount' => $discount
            ]);
        }

        // 7️⃣ Calculate total price AFTER applying discount
        $totalPrice = $basePrice - $discount;
        
        \Log::info('Final price calculation:', [
            'base_price' => $basePrice,
            'discount' => $discount,
            'total_price' => $totalPrice
        ]);

        // 8️⃣ Get user ID
        $userId = Auth::guard('guest')->id() ?? 1;

        // 9️⃣ Create booking
        $booking = Booking::create([
            'user_id' => $userId,
            'guest_id' => $guest->id,
            'room_id' => $request->room_id,
            'offer_id' => $appliedOfferId,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'number_of_guests' => $request->number_of_guests,
            'total_price' => $totalPrice,
            'special_requests' => $request->special_requests,
            'booking_status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        // 🔔 Send new booking notification to admins
        $this->notificationService->notifyNewBookingWithDetails($booking);

        // Notify about new guest registration if this is a new guest
        if ($guest->wasRecentlyCreated) {
            $this->notificationService->notifyNewGuestRegistration($guest);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'booking_id' => $booking->id,
                'booking_status' => $booking->booking_status,
                'total_price' => $totalPrice,
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $booking->check_out_date,
                'room_type' => $room->type->name,
                'guest_name' => $request->guest_info['name'],
                'applied_offer_id' => $appliedOfferId,
                'discount_amount' => $discount
            ],
            'message' => 'Room booked successfully. Please complete payment to confirm your booking.'
        ]);

    } catch (\Exception $e) {
        // Notify system error
        
        
        return response()->json([
            'success' => false,
            'message' => 'Error booking room: ' . $e->getMessage()
        ], 500);
    }
}
public function getRoomTypes()
{
    try {
        $roomTypes = RoomType::select(
                'room_types.id',
                'room_types.name',
                'room_types.description'
            )
            ->join('rooms', 'room_types.id', '=', 'rooms.type_id')
            ->where('rooms.status', 'available')
            ->groupBy('room_types.id', 'room_types.name', 'room_types.description')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $roomTypes,
            'message' => 'Room types retrieved successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve room types: ' . $e->getMessage()
        ], 500);
    }
}

 public function getUserInfo(Request $request)
    {
        try {
            $user = Auth::guard('guest')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'identification_number' => $user->identification_number,
                    'created_at' => $user->created_at,
                ],
                'message' => 'User information retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to retrieve user info: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user information'
            ], 500);
        }
    }

    /**
     * Update user information
     */
    public function updateUserInfo(Request $request)
    {
        try {
            $user = Auth::guard('guest')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'identification_number' => 'sometimes|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($request->only(['name', 'phone', 'identification_number']));

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'identification_number' => $user->identification_number,
                ],
                'message' => 'User information updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update user info: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user information'
            ], 500);
        }
    }
    /**
     * Calculate price for a room
     */
    public function calculatePrice(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'check_in_date' => 'required|date|after:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'offer_id' => 'nullable|exists:offers,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $room = Room::findOrFail($id);
            
            $checkIn = new \DateTime($request->check_in_date);
            $checkOut = new \DateTime($request->check_out_date);
            $nights = $checkIn->diff($checkOut)->days;

            $basePrice = $room->price_per_night * $nights;
            $discount = 0;

            if ($request->offer_id) {
    $offer = Offer::where('id', $request->offer_id)
        ->where('is_active', true)
        ->where('start_date', '<=', $request->check_in_date) // Use booking dates, not current date
        ->where('end_date', '>=', $request->check_out_date)
        ->whereHas('rooms', function($query) use ($room) {
            $query->where('rooms.id', $room->id);
        })
        ->first();

    if ($offer) {
        $discount = ($basePrice * $offer->discount_percentage) / 100;
    }
}

            $totalPrice = $basePrice - $discount;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'base_price' => $basePrice,
                    'discount' => $discount,
                    'total_price' => $totalPrice,
                    'nights' => $nights
                ],
                'message' => 'Price calculated successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating price: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment with comprehensive notifications
     */
    public function processPayment(Request $request, $bookingId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'payment_method' => 'required|string|in:credit_card,bank_transfer',
                'card_number' => 'required_if:payment_method,credit_card',
                'expiry_date' => 'required_if:payment_method,credit_card',
                'cvv' => 'required_if:payment_method,credit_card',
                'bank_name' => 'required_if:payment_method,bank_transfer',
                'account_number' => 'required_if:payment_method,bank_transfer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $booking = Booking::with('room')->findOrFail($bookingId);
            
            // Check authorization
            $guestId = Auth::guard('guest')->id();
            if ($booking->guest_id !== $guestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to pay for this booking'
                ], 403);
            }
            
            // Verify booking can be paid
            if ($booking->booking_status !== 'pending' || $booking->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot process payment for this booking'
                ], 400);
            }
            
            // Process payment
            $paymentResult = $this->processPaymentByMethod($request, $booking);
            
            if (!$paymentResult['success']) {
                // Notify payment failure
                $this->notificationService->notifyPaymentFailedWithDetails(
                    $this->createDummyPayment($booking), 
                    $paymentResult['message'],
                    $request->retry_count ?? 0
                );
                
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message']
                ], 400);
            }

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $bookingId,
                'payment_method' => $request->payment_method,
                'amount' => $booking->total_price,
                'status' => $request->payment_method === 'credit_card' ? 'completed' : 'pending',
                'transaction_id' => $paymentResult['transaction_id'],
                'receipt_url' => $paymentResult['receipt_url'],
                'payment_date' => now(),
                'payment_details' => $request->payment_method === 'credit_card' ? json_encode([
                    'card_last_four' => substr($request->card_number, -4),
                    'expiry_date' => $request->expiry_date
                ]) : json_encode([
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number
                ])
            ]);

            // Update booking status
if ($request->payment_method === 'credit_card') {
    $booking->update([
        'payment_status' => 'paid',
        'booking_status' => 'confirmed'
    ]);

    // Update room status only for completed payments
    $booking->room->update([
        'status' => 'booked'
    ]);
} else if ($request->payment_method === 'bank_transfer') {
    $booking->update([
        'payment_status' => 'pending',   // حالة الدفع قيد الانتظار
        'booking_status' => 'pending'
    ]);

    // لا نغير حالة الغرفة بعد
}


            // Generate invoice
            $invoice = $this->generateInvoice($payment);

            // 🔔 Send payment success notification
            $this->notificationService->notifyPaymentReceived($payment);
            
            // 🔔 Send booking confirmation notification
            $this->notifyBookingConfirmed($booking);

                    return response()->json([
            'success' => true,
            'message' => $request->payment_method === 'credit_card' ? 'Payment processed successfully' : 'Bank transfer submitted',
            'data' => [
                'payment_id' => $payment->id,
                'invoice_number' => $invoice->invoice_number,
                'receipt_url' => $payment->receipt_url,
                'booking_status' => $request->payment_method === 'credit_card' ? 'confirmed' : 'pending',
                'payment_status' => $request->payment_method === 'credit_card' ? 'paid' : 'pending',
                'room_status' => $request->payment_method === 'credit_card' ? 'booked' : 'pending',
                'payment_method' => $request->payment_method, // ✅ مهم جداً
                'transaction_id' => $payment->transaction_id
            ]
        ]);

        } catch (\Exception $e) {
            // Notify payment system error
            
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel booking with notifications
     */
/**
 * Cancel booking with notifications
 */
public function cancelBooking(Request $request, $bookingId)
{
    try {
        $guest = Auth::guard('guest')->user();
        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $booking = Booking::with(['room', 'guest'])
            ->where('id', $bookingId)
            ->where('guest_id', $guest->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        if (!in_array($booking->booking_status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be cancelled'
            ], 400);
        }

        $previousStatus = $booking->booking_status;
        $booking->update(['booking_status' => 'cancelled']);

        // حفظ تاريخ الحالة
        BookingStatusHistory::create([
            'booking_id' => $booking->id,
            'status' => 'cancelled',
            'changed_by' => 'guest',
            'notes' => 'Cancelled by guest'
        ]);

        // 🔔 إرسال إشعار إلغاء الحجز
        $this->notificationService->notifyBookingCancelled(
            $booking,
            "Booking #{$booking->id} cancelled by guest. Previous status: {$previousStatus}"
        );

        // إذا كانت الغرفة محجوزة → تصبح متاحة
        if ($booking->room && $booking->room->status === 'booked') {
            $booking->room->update(['status' => 'available']);

            // 🔔 إشعار بتوفر الغرفة
            $this->notificationService->notifyRoomAvailable($booking->room);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully with notification.'
        ]);

    } catch (\Exception $e) {
        // 🔔 إشعار بوجود مشكلة في النظام
       

        return response()->json([
            'success' => false,
            'message' => 'Failed to cancel booking: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get payment history for a booking
     */
    public function getPaymentHistory($bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            // Check if user is authorized to view this booking's payment history
            $guestId = Auth::guard('guest')->id();
            if ($booking->guest_id !== $guestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view payment history for this booking'
                ], 403);
            }
            
            $payments = Payment::where('booking_id', $bookingId)
                ->with('invoice')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payment history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if the authenticated user has an active booking
     */
    public function checkActiveBooking(Request $request)
{
    try {
        // Get authenticated user via "guest" guard
        $user = Auth::guard('guest')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
                'hasActiveBooking' => false
            ], 401);
        }

        $currentDate = Carbon::now()->toDateString();

        // Check for any confirmed booking within current dates
        $activeBooking = Booking::with('room')
            ->where('guest_id', $user->id)
            ->where('booking_status', 'confirmed')
            ->where('check_in_date', '<=', $currentDate)
            ->where('check_out_date', '>=', $currentDate)
            ->first();

        return response()->json([
            'success' => true,
            'hasActiveBooking' => !is_null($activeBooking),
            'booking' => $activeBooking ? [
                'id' => $activeBooking->id,
                'check_in_date' => $activeBooking->check_in_date,
                'check_out_date' => $activeBooking->check_out_date,
                'room_number' => optional($activeBooking->room)->room_number,
            ] : null
        ]);

    } catch (\Exception $e) {
        // Log error for further analysis
        \Log::error('Error checking active booking: '.$e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error checking booking status',
            'hasActiveBooking' => false
        ], 500);
    }
}

    /**
     * Get all active bookings for the authenticated user
     */
    public function getActiveBookings(Request $request)
    {
        try {
            $user = Auth::guard('guest')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $currentDate = Carbon::now()->toDateString();
            
            $activeBookings = Booking::with(['room', 'room.type'])
                ->where('guest_id', $user->id)
                ->where('booking_status', 'confirmed')
                ->where('check_in_date', '<=', $currentDate)
                ->where('check_out_date', '>=', $currentDate)
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'check_in_date' => $booking->check_in_date,
                        'check_out_date' => $booking->check_out_date,
                        'room_number' => $booking->room->room_number,
                        'room_type' => $booking->room->type->name,
                        'total_price' => $booking->total_price
                    ];
                });
            
            return response()->json([
                'success' => true,
                'bookings' => $activeBookings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving active bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a specific booking is active
     */
    public function checkBookingStatus($bookingId)
    {
        try {
            $user = Auth::guard('guest')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $currentDate = Carbon::now()->toDateString();
            
            $booking = Booking::with(['room', 'room.type'])
                ->where('id', $bookingId)
                ->where('guest_id', $user->id)
                ->first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }
            
            $isActive = $booking->booking_status === 'confirmed' &&
                       $booking->check_in_date <= $currentDate &&
                       $booking->check_out_date >= $currentDate;
            
            return response()->json([
                'success' => true,
                'isActive' => $isActive,
                'booking' => [
                    'id' => $booking->id,
                    'status' => $booking->booking_status,
                    'check_in_date' => $booking->check_in_date,
                    'check_out_date' => $booking->check_out_date,
                    'room_number' => $booking->room->room_number,
                    'room_type' => $booking->room->type->name
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking booking status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room reviews
     */
    public function getRoomReviews($id)
    {
        try {
            $reviews = Review::with('guest')
                ->where('room_id', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'guest_name' => $review->guest->name,
                        'created_at' => $review->created_at,
                    ];
                });

            $averageRating = Review::where('room_id', $id)->avg('rating');
            $ratingCounts = Review::where('room_id', $id)
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray();

            // Check if authenticated user has an active booking for this room
            $hasActiveBooking = false;
            if (Auth::guard('guest')->check()) {
                $userId = Auth::guard('guest')->id();
                $currentDate = Carbon::now()->toDateString();
                
                $hasActiveBooking = Booking::where('guest_id', $userId)
                    ->where('room_id', $id)
                    ->where('booking_status', 'confirmed')
                    ->where('check_in_date', '<=', $currentDate)
                    ->where('check_out_date', '>=', $currentDate)
                    ->exists();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'reviews' => $reviews,
                    'average_rating' => round($averageRating, 1),
                    'rating_counts' => $ratingCounts,
                    'has_active_booking' => $hasActiveBooking,
                ],
                'message' => 'Reviews retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve reviews: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit a review for a room with notifications
     */
    public function submitReview(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if user has an active booking for this room
            $userId = Auth::guard('guest')->id();
            $currentDate = Carbon::now()->toDateString();
            
            $activeBooking = Booking::where('guest_id', $userId)
                ->where('room_id', $id)
                ->where('booking_status', 'confirmed')
                ->where('check_in_date', '<=', $currentDate)
                ->where('check_out_date', '>=', $currentDate)
                ->first();

            if (!$activeBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to have an active booking to review this room'
                ], 403);
            }

            // Check if user already reviewed this room from this booking
            $existingReview = Review::where('guest_id', $userId)
                ->where('room_id', $id)
                ->where('booking_id', $activeBooking->id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this room for this booking'
                ], 409);
            }

            // Create the review
            $review = Review::create([
                'guest_id' => $userId,
                'room_id' => $id,
                'booking_id' => $activeBooking->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            // 🔔 Send new review notification
            $this->notificationService->notifyNewReview($review);

            // If rating is low, send urgent complaint notification
            if ($request->rating <= 2) {
                $this->notificationService->notifyUrgentComplaint($review);
            }

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'data' => $review
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booked dates for a specific room
     */
    public function getBookedDates($id)
    {
        try {
            $room = Room::findOrFail($id);
            
            // Get all confirmed and pending bookings for this room
            $bookings = Booking::where('room_id', $id)
                ->whereIn('booking_status', ['confirmed', 'pending'])
                ->get();
            
            // Extract booked date ranges
            $bookedDates = [];
            foreach ($bookings as $booking) {
                $checkIn = Carbon::parse($booking->check_in_date);
                $checkOut = Carbon::parse($booking->check_out_date);
                
                // Add all dates between check-in and check-out (excluding check-out)
                $currentDate = $checkIn->copy();
                while ($currentDate->lt($checkOut)) {
                    $bookedDates[] = $currentDate->format('Y-m-d');
                    $currentDate->addDay();
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'booked_dates' => array_unique($bookedDates),
                    'room_id' => $id
                ],
                'message' => 'Booked dates retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving booked dates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest check-in with notifications
     */
    public function guestCheckIn(Request $request, $bookingId)
    {
        try {
            $booking = Booking::with(['room', 'guest'])->findOrFail($bookingId);
            
            // Check authorization
            $guestId = Auth::guard('guest')->id();
            if ($booking->guest_id !== $guestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to check in for this booking'
                ], 403);
            }

            // Validate check-in conditions
            $checkInDate = Carbon::parse($booking->check_in_date);
            $today = Carbon::today();
            
            if ($today->lt($checkInDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in is only allowed on or after ' . $checkInDate->format('Y-m-d')
                ], 400);
            }

            if ($booking->booking_status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only confirmed bookings can check in'
                ], 400);
            }

            // Update booking status
            $booking->update([
                'booking_status' => 'checked_in',
                'actual_check_in' => now()
            ]);

            // Update room status
            $booking->room->update([
                'status' => 'occupied'
            ]);

            // 🔔 Send check-in notification
            $this->notifyGuestCheckIn($booking);

            // 🔔 Create cleaning schedule for after check-out
            $this->schedulePostCheckoutCleaning($booking);

            return response()->json([
                'success' => true,
                'message' => 'Check-in completed successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'room_number' => $booking->room->room_number,
                    'check_in_time' => now()->format('H:i:s'),
                    'room_status' => 'occupied'
                ]
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Check-in failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guest check-out with notifications
     */
    public function guestCheckOut(Request $request, $bookingId)
    {
        try {
            $booking = Booking::with(['room', 'guest'])->findOrFail($bookingId);
            
            // Check authorization
            $guestId = Auth::guard('guest')->id();
            if ($booking->guest_id !== $guestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to check out for this booking'
                ], 403);
            }

            if ($booking->booking_status !== 'checked_in') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only checked-in bookings can check out'
                ], 400);
            }

            // Update booking status
            $booking->update([
                'booking_status' => 'checked_out',
                'actual_check_out' => now()
            ]);

            // Update room status to needs cleaning
            $booking->room->update([
                'status' => 'cleaning_required'
            ]);

            // 🔔 Send check-out notification
            $this->notifyGuestCheckOut($booking);

            // 🔔 Trigger immediate cleaning notification
            $this->notificationService->notifyCleaningRequired(
                $booking->room, 
                "Post check-out cleaning required"
            );

            return response()->json([
                'success' => true,
                'message' => 'Check-out completed successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'room_number' => $booking->room->room_number,
                    'check_out_time' => now()->format('H:i:s'),
                    'room_status' => 'cleaning_required'
                ]
            ]);

        } catch (\Exception $e) {
            
            
            return response()->json([
                'success' => false,
                'message' => 'Check-out failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request room service with notifications
     */
    public function requestRoomService(Request $request, $bookingId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'service_type' => 'required|string|in:cleaning,maintenance,amenity,food',
                'description' => 'required|string|max:500',
                'urgency' => 'required|string|in:low,medium,high,urgent'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $booking = Booking::with(['room', 'guest'])->findOrFail($bookingId);
            
            // Check authorization and active booking
            $guestId = Auth::guard('guest')->id();
            if ($booking->guest_id !== $guestId || $booking->booking_status !== 'checked_in') {
                return response()->json([
                    'success' => false,
                    'message' => 'Service can only be requested during active stay'
                ], 403);
            }

            // 🔔 Send room service request notification
            $this->notifyRoomServiceRequest(
                $booking,
                $request->service_type,
                $request->description,
                $request->urgency
            );

            return response()->json([
                'success' => true,
                'message' => 'Room service request submitted successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'room_number' => $booking->room->room_number,
                    'service_type' => $request->service_type,
                    'urgency' => $request->urgency,
                    'requested_at' => now()
                ]
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Service request failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create payment intent for Stripe
     */
    public function createPaymentIntent($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $booking->total_price * 100,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        return response()->json([
            'success' => true,
            'client_secret' => $paymentIntent->client_secret
        ]);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Helper function to format room data
     */
private function formatRoomData($room)
{
    $primaryImage = $room->images->where('is_primary', true)->first();
    if (!$primaryImage) {
        $primaryImage = $room->images->first();
    }
    
    // Build proper image URLs
    $primaryImageUrl = null;
    if ($primaryImage) {
        $primaryImageUrl = $this->buildImageUrl($primaryImage->image_url);
    }

    $activeOffers = $room->offers->map(function($offer) {
        return [
            'id' => $offer->id,
            'title' => $offer->title,
            'description' => $offer->description,
            'discount_percentage' => $offer->discount_percentage,
            'promo_code' => $offer->promo_code,
            'end_date' => $offer->end_date,
        ];
    });

    $avgRating = (float) round($room->reviews()->avg('rating') ?: 0, 1);

    $ratingLabel = 'No reviews yet';
    if ($avgRating >= 4.5) $ratingLabel = 'Excellent';
    elseif ($avgRating >= 4) $ratingLabel = 'Very Good';
    elseif ($avgRating >= 3) $ratingLabel = 'Good';
    elseif ($avgRating >= 2) $ratingLabel = 'Fair';
    elseif ($avgRating > 0) $ratingLabel = 'Poor';

    return [
        'id' => $room->id,
        'room_number' => $room->room_number,
        'type' => $room->type ? $room->type->name : null,
        'max_occupancy' => $room->type ? $room->type->max_occupancy : null,
        'floor_number' => $room->floor_number,
        'price_per_night' => $room->price_per_night,
        'description' => $room->description,
        'status' => $room->status,
        'primary_image' => $primaryImageUrl,
        'images' => $room->images->map(function($img) {
            return [
                'id' => $img->id,
                'url' => $this->buildImageUrl($img->image_url),
                'is_primary' => $img->is_primary,
            ];
        }),
        'amenities' => $room->amenities->map(fn($a) => [
            'id' => $a->id,
            'name' => $a->name,
            'icon' => $a->icon_class,
        ]),
        'offers' => $activeOffers,
        'average_rating' => $avgRating,
        'rating_label' => $ratingLabel,
        'reviews_count' => $room->reviews()->count(),
        'reviews' => $room->reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'guest_name' => $review->guest_name,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at,
            ];
        }),
        'maintenance_required' => $room->status === 'maintenance',
    ];
}
private function buildImageUrl($imagePath)
{
    if (!$imagePath) {
        return null;
    }
    
    // If it's already a full URL, return as is
    if (str_starts_with($imagePath, 'http')) {
        return $imagePath;
    }
    
    // If it's an S3 path, build the proxy URL
    if (str_contains($imagePath, 'staynest-images.s3.eu-central-2.idrivee2.com')) {
        // Extract just the filename/path after the domain
        $path = str_replace('https://staynest-images.s3.eu-central-2.idrivee2.com/', '', $imagePath);
        return url("/api/images/proxy/" . urlencode($path));
    }
    
    // For local paths, build proxy URL
    return url("/api/images/proxy/" . urlencode($imagePath));
}
    /**
     * Process payment based on method
     */
    private function processPaymentByMethod($request, $booking)
    {
        switch ($request->payment_method) {
            case 'credit_card':
                return $this->processCreditCardPayment($request, $booking);
            case 'bank_transfer':
                return $this->processBankTransfer($request, $booking);
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    /**
     * Process credit card payment using Stripe
     */
    private function processCreditCardPayment($request, $booking)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $booking->total_price * 100,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'room_number' => $booking->room->room_number
                ]
            ]);

            return [
                'success' => true,
                'transaction_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'receipt_url' => $this->generateReceiptUrl($booking, $paymentIntent->id)
            ];

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return [
                'success' => false,
                'message' => 'Stripe API error: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process bank transfer
     */
    private function processBankTransfer($request, $booking)
    {
        // Generate unique transaction ID
        $transactionId = 'BT_' . time() . '_' . $booking->id;
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'receipt_url' => $this->generateReceiptUrl($booking, $transactionId)
        ];
    }

public function proxyImage($filename)
{
    try {
        $filename = urldecode($filename);
        
        \Log::info('Proxy image request:', ['filename' => $filename]);
        
        // Build S3 URL
        $s3Url = "https://staynest-images.s3.eu-central-2.idrivee2.com/{$filename}";
        
        \Log::info('S3 URL:', ['url' => $s3Url]);
        
        // Create client with AWS signature
        $client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'verify' => false,
        ]);
        
        // Create request with proper headers
        $request = new \GuzzleHttp\Psr7\Request('GET', $s3Url, [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);
        
        // Sign the request with AWS credentials
        $signer = new \Aws\Signature\SignatureV4('s3', 'eu-central-2');
        $credentials = new \Aws\Credentials\Credentials(
            env('AWS_ACCESS_KEY_ID'),
            env('AWS_SECRET_ACCESS_KEY')
        );
        
        $signedRequest = $signer->signRequest($request, $credentials);
        
        $response = $client->send($signedRequest);
        
        $contentType = $response->getHeader('Content-Type')[0] ?? 
                      $this->mimeContentTypeFromFilename($filename);
        
        return response($response->getBody(), 200)
            ->header('Content-Type', $contentType)
            ->header('Cache-Control', 'public, max-age=86400')
            ->header('Access-Control-Allow-Origin', '*');
            
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        \Log::error('Proxy image request failed:', [
            'filename' => $filename,
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response'
        ]);
        
        // Return default image
        $defaultImagePath = public_path('default-room-image.jpg');
        if (file_exists($defaultImagePath)) {
            return response()->file($defaultImagePath);
        }
        
        return response()->json([
            'error' => 'Image not found',
            'message' => $e->getMessage()
        ], 404);
        
    } catch (\Exception $e) {
        \Log::error('Proxy image error:', [
            'filename' => $filename,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'error' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

private function mimeContentTypeFromFilename($filename)
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $mime_types = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
    ];
    
    return $mime_types[$ext] ?? 'image/jpeg';
}
    /**
     * Generate receipt URL
     */
    private function generateReceiptUrl($booking, $transactionId)
    {
        return url("/receipts/{$transactionId}");
    }

    /**
     * Generate invoice
     */
    private function generateInvoice($payment)
    {
        $issueDate = now();
        $dueDate = $issueDate->copy()->addDays(30);

        return Invoice::create([
            'payment_id' => $payment->id,
            'invoice_number' => 'INV_' . time() . '_' . $payment->id,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'amount' => $payment->amount,
            'status' => 'paid'
        ]);
    }

    /**
     * Helper method to create dummy payment for notification
     */
    private function createDummyPayment($booking)
    {
        return new Payment([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => 'credit_card',
            'status' => 'failed'
        ]);
    }

    /**
     * Helper method to schedule post-checkout cleaning
     */
    private function schedulePostCheckoutCleaning($booking)
    {
        try {
            $cleaningSchedule = CleaningSchedule::create([
                'room_id' => $booking->room_id,
                'cleaner_id' => 1, // Default cleaner
                'cleaning_date' => Carbon::parse($booking->check_out_date)->addDay(),
                'cleaning_status' => 'scheduled',
                'priority_level' => 'high',
                'notes' => 'Post check-out cleaning'
            ]);

            // 🔔 Notify cleaning scheduled
            $this->notifyCleaningScheduled($cleaningSchedule);

        } catch (\Exception $e) {
            // Log error but don't fail the check-in process
            \Log::error('Failed to schedule cleaning: ' . $e->getMessage());
        }
    }

    // ==================== NOTIFICATION HELPER METHODS ====================

    /**
     * Notify when booking is confirmed after payment
     */
    private function notifyBookingConfirmed(Booking $booking)
{
    $notification = new \App\Notifications\BookingNotification(
        booking: $booking,
        title: 'Booking Confirmed',
        message: "Booking #{$booking->id} has been confirmed for room {$booking->room->room_number}",
        category: 'booking',
        priority: 'normal',
        icon: 'check-circle'
    );

    $this->notifyAdmins($notification, 'staff');
}

    /**
     * Notify guest check-in
     */
    private function notifyGuestCheckIn(Booking $booking)
{
    $notification = new \App\Notifications\BookingNotification(
        booking: $booking,
        title: '🚪 Guest Checked In',
        message: "{$booking->guest->name} has checked into room {$booking->room->room_number}",
        category: 'booking',
        priority: 'normal',
        icon: 'log-in'
    );

    $this->notifyAdmins($notification, 'staff');
}


    /**
     * Notify guest check-out
     */
    private function notifyGuestCheckOut(Booking $booking)
{
    $notification = new \App\Notifications\BookingNotification(
        booking: $booking,
        title: '🚪 Guest Checked Out',
        message: "{$booking->guest->name} has checked out from room {$booking->room->room_number}",
        category: 'booking',
        priority: 'normal',
        icon: 'log-out'
    );

    $this->notifyAdmins($notification, 'staff');
}


    /**
     * Notify room service request
     */
    private function notifyRoomServiceRequest(Booking $booking, $serviceType, $description, $urgency)
{
    $priorityMap = [
        'urgent' => 'urgent',
        'high' => 'high',
        'medium' => 'normal',
        'low' => 'low'
    ];

    $notification = new \App\Notifications\RoomServiceNotification(
        booking: $booking,
        title: "🛎️ Room Service Request - " . ucfirst($serviceType),
        message: "Room {$booking->room->room_number}: {$description}",
        category: 'room_service',
        priority: $priorityMap[$urgency] ?? 'normal',
        icon: 'bell'
    );

    $this->notifyAdmins($notification, 'staff');
}


    /**
     * Notify cleaning scheduled
     */
private function notifyCleaningScheduled(CleaningSchedule $cleaning)
{
    $notification = new \App\Notifications\RoomNotification(
        $cleaning->room, // 🌟 أول باراميتر يجب أن يكون Room
        '🧹 Cleaning Scheduled',
        "Room {$cleaning->room->room_number} scheduled for cleaning on {$cleaning->cleaning_date}",
        'cleaning',
        'normal',
        'calendar'
    );

    $this->notifyAdmins($notification, 'supervisor');
}



    /**
     * Send notification to admin users
     */
    private function notifyAdmins($notification, $minRole = 'supervisor')
    {
        $admins = \App\Models\User::whereHas('role', function($query) use ($minRole) {
            $roles = ['admin', 'manager', 'supervisor', 'staff'];
            $minIndex = array_search($minRole, $roles);
            $allowedRoles = $minIndex !== false ? array_slice($roles, 0, $minIndex + 1) : $roles;
            $query->whereIn('role_name', $allowedRoles);
        })->where('is_active', true)->get();

        \Illuminate\Support\Facades\Notification::send($admins, $notification);
    }
    
}