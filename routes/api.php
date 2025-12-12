<?php

use App\Http\Controllers\AmenityController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\RoomServiceOrderController;
use App\Http\Controllers\BookingStatusHistoryController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CleaningScheduleController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GuestAuthController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GuestProfileController;
use App\Http\Controllers\GuestRoomController;
use App\Http\Controllers\GuestRoomServiceController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LegalMentionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\RoomAmenityController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomImageController;
use App\Http\Controllers\RoomOfferController;
use App\Http\Controllers\RoomServiceCategoryController;
use App\Http\Controllers\RoomServiceItemController;
use App\Http\Controllers\RoomServiceOrderItemController;
use App\Http\Controllers\UserController;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

// routes/api.php
// About page data routes
Route::get('/home-page', [AboutController::class, 'getHomePageData']);
Route::get('/rooms/count', [AboutController::class, 'getRoomsCount']);
Route::get('/guests/count', [AboutController::class, 'getGuestsCount']);
Route::get('/staff/count', [AboutController::class, 'getStaffCount']);
Route::apiResource('legal-mentions', LegalMentionController::class);
Route::apiResource('faqs', FaqController::class);
Route::post('faqs/reorder', [FaqController::class, 'reorder']);

Route::apiResource('blogs', BlogController::class);
Route::get('blogs/{id}/show', [BlogController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('home-pages', HomePageController::class);
    Route::post('/home-pages/upload-logo', [HomePageController::class, 'uploadLogo']);
});

// Public route to get home page data
Route::get('/home-page', [HomePageController::class, 'index']);
Route::prefix('contact-messages')->group(function () {
    Route::get('/', [ContactMessageController::class, 'index']);
    Route::get('/statistics', [ContactMessageController::class, 'statistics']);
    Route::get('/{id}', [ContactMessageController::class, 'show']);
    Route::post('/', [ContactMessageController::class, 'store']);
    Route::put('/{id}/status', [ContactMessageController::class, 'updateStatus']);
    Route::post('/{id}/response', [ContactMessageController::class, 'sendResponse']);
    Route::delete('/{id}', [ContactMessageController::class, 'destroy']);
});


Route::delete('rooms/{room}', [RoomController::class, 'destroy']);
    

Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'getAllData']);
    Route::get('/overview', [DashboardController::class, 'getOverview']);
    Route::get('/booking-trends', [DashboardController::class, 'getBookingTrends']);
    Route::get('/revenue-breakdown', [DashboardController::class, 'getRevenueBreakdown']);
    Route::get('/room-status', [DashboardController::class, 'getRoomStatus']);
    Route::get('/top-services', [DashboardController::class, 'getTopServices']);
    Route::get('/user-growth', [DashboardController::class, 'getUserGrowth']);
    Route::get('/ratings', [DashboardController::class, 'getRatingsDistribution']);
    Route::get('/last-bookings', [DashboardController::class, 'getLastBookings']);
    Route::get('/latest-payments', [DashboardController::class, 'getLatestPayments']);
    Route::get('/recent-reviews', [DashboardController::class, 'getRecentReviews']);
    Route::get('/active-rooms', [DashboardController::class, 'getActiveRooms']);
});
Route::get('/reviews', [ReviewController::class, 'index']);
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
Route::get('/cleaners', [CleaningScheduleController::class, 'getCleaners']);
Route::apiResource('invoices', InvoiceController::class);
Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download']);
Route::get('invoices/{invoice}/view', [InvoiceController::class, 'view']);
Route::apiResource('users', UserController::class);
Route::post('users/{user}/block', [UserController::class, 'blockToggle']);
Route::apiResource('guests', GuestController::class);
Route::post('guests/{guest}/toggle-block', [GuestController::class, 'toggleBlock']);
Route::apiResource('permissions', PermissionController::class);
Route::apiResource('roles', RoleController::class);
Route::prefix('roles')->group(function () {
    Route::get('/{role}/permissions', [RolePermissionController::class, 'getPermissions']);
    Route::post('/{role}/permissions', [RolePermissionController::class, 'updatePermissions']);
});
Route::prefix('room-service-order-items')->group(function () {
    Route::get('/', [RoomServiceOrderItemController::class, 'index']);
    Route::post('/', [RoomServiceOrderItemController::class, 'store']);
    Route::get('/{id}', [RoomServiceOrderItemController::class, 'show']);
    Route::put('/{id}', [RoomServiceOrderItemController::class, 'update']);
    Route::delete('/{id}', [RoomServiceOrderItemController::class, 'destroy']);
});
Route::prefix('room-service-orders')->group(function () {
    Route::get('/', [RoomServiceOrderController::class, 'index']);
    Route::post('/', [RoomServiceOrderController::class, 'store']);
    Route::get('/{id}', [RoomServiceOrderController::class, 'show']);
    Route::put('/{id}', [RoomServiceOrderController::class, 'update']);
    Route::delete('/{id}', [RoomServiceOrderController::class, 'destroy']);
    
    // Additional custom endpoints
    Route::get('/status/{status}', [RoomServiceOrderController::class, 'getByStatus']);
    Route::get('/guest/{guestId}', [RoomServiceOrderController::class, 'getByGuest']);
    Route::get('/room/{roomId}', [RoomServiceOrderController::class, 'getByRoom']);
});
Route::apiResource('room-service-categories', RoomServiceCategoryController::class);
Route::apiResource('room-service-items', RoomServiceItemController::class);
Route::get('bookings/active', [PaymentController::class, 'getBookings']);
// Guest Password Reset API Routes
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/auth/google', [AuthController::class, 'googleAuth']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/room-types', [RoomTypeController::class, 'index']);
Route::prefix('room-types')->group(function () {
    Route::get('/', [RoomTypeController::class, 'index']);
    Route::post('/', [RoomTypeController::class, 'store']);
    Route::get('/{id}', [RoomTypeController::class, 'show']);
    Route::put('/{id}', [RoomTypeController::class, 'update']);
    Route::delete('/{id}', [RoomTypeController::class, 'destroy']);
    Route::get('/available', [RoomTypeController::class, 'available']);
});         
Route::prefix('rooms')->group(function () {
    Route::get('/', [RoomController::class, 'index']);
    Route::post('/', [RoomController::class, 'store']);
    Route::get('/{id}', [RoomController::class, 'show']);
Route::put('/{room}', [RoomController::class, 'update']);
    Route::delete('/{id}', [RoomController::class, 'destroy']);
    Route::get('/available', [RoomController::class, 'available']);
});
Route::prefix('rooms/{room}/images')->group(function () {
    Route::get('/', [RoomImageController::class, 'index']);
    Route::post('/', [RoomImageController::class, 'store']);
    Route::post('/{image}/primary', [RoomImageController::class, 'setPrimary']);
    Route::delete('/{image}', [RoomImageController::class, 'destroy']);
});

Route::prefix('amenities')->group(function () {
    Route::get('/', [AmenityController::class, 'index']);
    Route::post('/', [AmenityController::class, 'store']);
    Route::get('/{amenity}', [AmenityController::class, 'show']);
    Route::put('/{amenity}', [AmenityController::class, 'update']);
    Route::delete('/{amenity}', [AmenityController::class, 'destroy']);
});
Route::apiResource('room-amenities', RoomAmenityController::class);
Route::apiResource('offers', OfferController::class);
Route::post('offers/{offer}/toggle-status', [OfferController::class, 'toggleStatus']);
Route::apiResource('room-offers', RoomOfferController::class);
Route::get('available-rooms/{offer}', [RoomOfferController::class, 'getAvailableRooms']);
Route::get('available-offers/{room}', [RoomOfferController::class, 'getAvailableOffers']);
Route::apiResource('bookings', BookingController::class);
Route::get('/guests', function () {
    return response()->json(Guest::all());
});
Route::get('/users', function () {
    return response()->json(User::all());
});

    Route::get('/bookings/{bookingId}/status-history', [BookingStatusHistoryController::class, 'index']);
Route::post('/bookings/{booking}/status-history', [BookingStatusHistoryController::class, 'store']);
    Route::get('/bookings/{bookingId}/status-history/{id}', [BookingStatusHistoryController::class, 'show']);
    Route::delete('/bookings/{bookingId}/status-history/{id}', [BookingStatusHistoryController::class, 'destroy']);
Route::apiResource('payments', PaymentController::class);
Route::get('bookings/{bookingId}/payments', [PaymentController::class, 'getPaymentsByBooking']);
Route::get('/cleaning-schedules', [CleaningScheduleController::class, 'index']);
Route::post('/cleaning-schedules', [CleaningScheduleController::class, 'store']);
Route::get('/cleaning-schedules/{cleaningSchedule}', [CleaningScheduleController::class, 'show']);
Route::put('/cleaning-schedules/{cleaningSchedule}', [CleaningScheduleController::class, 'update']);
Route::delete('/cleaning-schedules/{cleaningSchedule}', [CleaningScheduleController::class, 'destroy']);
Route::delete('users/{user}', [UserController::class, 'destroy']);
// Cleaners API

Route::prefix('guest')->group(function () {
     Route::get('/rooms', [GuestRoomController::class, 'getRooms']);
    Route::get('/rooms/{id}', [GuestRoomController::class, 'getRoomDetails']);
    Route::post('/rooms/{id}/availability', [GuestRoomController::class, 'checkAvailability']);
 Route::get('/rooms/{id}/booking-data', [GuestRoomController::class, 'getBookingFormData']);


    Route::post('/bookings', [GuestRoomController::class, 'createBooking']);
        Route::post('/bookings/{id}/cancel', [GuestRoomController::class, 'cancelBooking']);
    Route::get('/rooms/{id}/calculate-price', [GuestRoomController::class, 'calculatePrice']);
});
Route::prefix('guest')->group(function () {
    Route::post('register', [GuestAuthController::class, 'register']);
    Route::post('login', [GuestAuthController::class, 'login']);
    Route::post('google-auth', [GuestAuthController::class, 'googleAuth']);
      Route::post('/verify-otp', [GuestAuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [GuestAuthController::class, 'resendOtp']);
    // Protected profile routes - use the correct guard
    Route::middleware('auth:guest')->group(function () {
        Route::get('/user', [GuestAuthController::class, 'guest']);
        Route::get('profile', [GuestProfileController::class, 'getProfile']);
        Route::put('profile', [GuestProfileController::class, 'updateProfile']);
        Route::put('password', [GuestProfileController::class, 'updatePassword']);
        Route::post('logout', [GuestAuthController::class, 'logout']);
            Route::post('/bookings/{id}/cancel', [GuestRoomController::class, 'cancelBooking']);
        Route::post('bookings/{bookingId}/payment', [GuestRoomController::class, 'processPayment']);
    Route::get('bookings/{bookingId}/payments', [GuestRoomController::class, 'getPaymentHistory']);
     Route::get('/check-active-booking', [GuestRoomController::class, 'checkActiveBooking']);
    Route::get('/active-bookings', [GuestRoomController::class, 'getActiveBookings']);
    Route::get('/booking-status/{bookingId}', [GuestRoomController::class, 'checkBookingStatus']);
    

    });
});

Route::get('/guest/rooms/{id}/reviews', [GuestRoomController::class, 'getRoomReviews']);
Route::post('/guest/rooms/{id}/reviews', [GuestRoomController::class, 'submitReview'])->middleware('auth:guest');
Route::middleware('auth:guest')->group(function () {
    // إنشاء intent للدفع
     Route::get('/guest/user-info', [GuestRoomController::class, 'getUserInfo']);
    Route::put('/guest/user-info', [GuestRoomController::class, 'updateUserInfo']);
    Route::post('/guest/bookings/{booking}/payment-intent', [GuestRoomController::class, 'createPaymentIntent']);
    
    // تنفيذ عملية الدفع
    Route::post('/guest/bookings/{booking}/payment', [GuestRoomController::class, 'processPayment']);
});
Route::group(['prefix' => 'guest/room-service', 'middleware' => ['auth:guest']], function () {
    Route::get('/menu', [GuestRoomServiceController::class, 'getMenu']);
    Route::post('/order', [GuestRoomServiceController::class, 'placeOrder']);
    Route::get('/orders', [GuestRoomServiceController::class, 'getOrderHistory']);
}); 
Route::middleware('auth:guest')->group(function () {
    // Get reviews for a booking
    Route::get('/bookings/{bookingId}/reviews', [GuestProfileController::class, 'getBookingReviews']);
    
    // Submit a review for a booking
    Route::post('/bookings/{bookingId}/reviews', [GuestProfileController::class, 'submitReview']);
    
    // Check review eligibility for a booking
    Route::get('/bookings/{bookingId}/review-eligibility', [GuestProfileController::class, 'checkReviewEligibility']);
});

// Room Review Routes (public)
Route::get('/rooms/{roomId}/reviews', [GuestProfileController::class, 'getRoomReviews']);
Route::middleware('auth:guest')->post('/guest/set-password', [GuestAuthController::class, 'setPassword']);
Route::get('/guest/rooms/{id}/booked-dates', [GuestRoomController::class, 'getBookedDates']);
Route::get('/guest/room-types', [GuestRoomController::class, 'getRoomTypes']);

// In routes/api.php
Route::get('/test-notifications', function (Request $request) {
    $user = $request->user();
    
    if (!$user) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }                   
    
    $notifications = $user->notifications()->get();
    
    return response()->json([
        'user_id' => $user->id,
        'user_email' => $user->email,
        'notifications_count' => $notifications->count(),
        'notifications' => $notifications
    ]);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
});
Route::get('/debug-notifications-detailed', function (Request $request) {
    $user = $request->user();
    
    if (!$user) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }
    
    $notifications = $user->notifications()
        ->orderBy('created_at', 'desc')
        ->get();
    
    $formattedNotifications = $notifications->map(function ($notification) {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'data' => $notification->data,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
            'updated_at' => $notification->updated_at,
            'parsed_data' => is_string($notification->data) ? json_decode($notification->data, true) : $notification->data
        ];
    });
    
    return response()->json([
        'user_id' => $user->id,
        'user_email' => $user->email,
        'notifications_count' => $notifications->count(),
        'notifications' => $formattedNotifications,
        'unread_count' => $user->unreadNotifications()->count()
    ]);
})->middleware('auth:sanctum');
Route::get('/images/proxy/{roomId}/{imageId}', function ($roomId, $imageId) {
    try {
        \Log::info("Proxy request for room: $roomId, image: $imageId");
        
        $image = \App\Models\RoomImage::where('room_id', $roomId)->findOrFail($imageId);
        \Log::info("Found image record:", ['image_url' => $image->image_url]);
        
        $imageUrl = $image->image_url;
        
        // Check if it's an S3 URL or local storage URL
        if (str_contains($imageUrl, 'staynest-images.s3.eu-central-2.idrivee2.com')) {
            // Handle S3 images
            $path = str_replace(Storage::disk('s3')->url(''), '', $imageUrl);
            \Log::info("S3 image - Extracted path: $path");
            
            if (!Storage::disk('s3')->exists($path)) {
                \Log::error("File not found in S3: $path");
                return response()->json(['error' => 'Image not found in S3 storage'], 404);
            }
            
            $file = Storage::disk('s3')->get($path);
            $mimeType = Storage::disk('s3')->mimeType($path);
            
        } else {
            // Handle local storage images
            \Log::info("Local storage image");
            
            // Extract the path from the local URL
            $path = str_replace(url('storage/'), '', $imageUrl);
            $fullPath = storage_path('app/public/' . $path);
            
            \Log::info("Local path: $fullPath");
            
            if (!file_exists($fullPath)) {
                \Log::error("Local file not found: $fullPath");
                return response()->json(['error' => 'Local image not found'], 404);
            }
            
            $file = file_get_contents($fullPath);
            $mimeType = mime_content_type($fullPath);
        }
        
        $fileSize = strlen($file);
        \Log::info("File retrieved successfully", [
            'mime_type' => $mimeType,
            'file_size' => $fileSize
        ]);
        
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=3600');
            
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        \Log::error("Image not found in database: " . $e->getMessage());
        return response()->json(['error' => 'Image not found'], 404);
    } catch (\Exception $e) {
        \Log::error("Proxy error: " . $e->getMessage());
        return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
});
Route::get('/debug-images/{roomId}', function ($roomId) {
    $images = \App\Models\RoomImage::where('room_id', $roomId)->get();
    
    return response()->json([
        'room_id' => $roomId,
        'total_images' => $images->count(),
        'images' => $images->map(function($image) {
            return [
                'id' => $image->id,
                'image_url' => $image->image_url,
                'is_primary' => $image->is_primary,
                'created_at' => $image->created_at
            ];
        })
    ]);
});
Route::get('/debug-all-images', function () {
    $images = \App\Models\RoomImage::all();
    
    return response()->json([
        'total_images' => $images->count(),
        'images_by_room' => $images->groupBy('room_id')->map(function($roomImages, $roomId) {
            return [
                'room_id' => $roomId,
                'count' => $roomImages->count(),
                'image_ids' => $roomImages->pluck('id')
            ];
        })
    ]);
});
Route::get('/images/proxy/{filename}', [GuestRoomController::class, 'proxyImage']);
Route::get('/debug-room-images/{roomId}', function($roomId) {
    $room = \App\Models\Room::with('images')->find($roomId);
    
    if (!$room) {
        return response()->json(['error' => 'Room not found'], 404);
    }
    
    $images = $room->images->map(function($image) {
        return [
            'id' => $image->id,
            'original_url' => $image->image_url,
            'proxy_url' => url("/api/images/proxy/" . urlencode($image->image_url)),
            'is_primary' => $image->is_primary,
            'exists_in_s3' => null // You'd need to check this
        ];
    });
    
    return response()->json([
        'room_id' => $roomId,
        'room_number' => $room->room_number,
        'images' => $images,
        'primary_image' => $room->images->where('is_primary', true)->first()
    ]);
});
Route::get('/debug-proxy-test', function() {
    $filename = 'room_images/MvIgEH4IHmaX4ZUAcHlDKyrHxlu01IlMifEYCt56.jpg';
    
    try {
        $s3Url = "https://staynest-images.s3.eu-central-2.idrivee2.com/{$filename}";
        
        \Log::info('Testing S3 URL: ' . $s3Url);
        
        $client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'verify' => false,
        ]);
        
        $response = $client->get($s3Url);
        
        return response()->json([
            'success' => true,
            'status_code' => $response->getStatusCode(),
            'content_type' => $response->getHeader('Content-Type')[0] ?? 'unknown',
            'message' => 'S3 access successful'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            's3_url' => $s3Url ?? 'not set'
        ], 500);
    }
});
Route::get('/debug-image/{filename}', function($filename) {
    try {
        $controller = new GuestRoomController(app(\App\Services\NotificationService::class));
        
        // Test direct S3 access
        $s3Path = "room_images/{$filename}";
        $s3Url = "https://staynest-images.s3.eu-central-2.idrivee2.com/{$s3Path}";
        
        $client = new \GuzzleHttp\Client();
        $response = $client->get($s3Url);
        
        return response()->json([
            'success' => true,
            'filename' => $filename,
            's3_url' => $s3Url,
            'status' => $response->getStatusCode(),
            'content_type' => $response->getHeader('Content-Type')[0] ?? 'unknown'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'filename' => $filename,
            'error' => $e->getMessage(),
            's3_url' => $s3Url ?? 'not set'
        ], 500);
    }
});
Route::get('/test-s3-access/{filename}', [GuestRoomController::class, 'testS3Access']);
Route::get('/debug-images', [RoomServiceItemController::class, 'debugImages']);
Route::get('/room-service-images/{filename}', [RoomServiceItemController::class, 'proxyImage']);
Route::get('/home-page-logo/{filename}', [HomePageController::class, 'proxyLogo']);
Route::get('blog-images/{filename}', [BlogController::class, 'proxyBlogImage']);
Route::get('/home-page-invoice', [HomePageController::class, 'getForInvoice']);