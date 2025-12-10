<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RoomServiceCategory;
use App\Models\RoomServiceItem;
use App\Models\RoomServiceOrder;
use App\Models\Booking;
use Carbon\Carbon;

class GuestRoomServiceController extends Controller
{
    /**
     * Get room service menu
     */
    public function getMenu(Request $request)
    {
        try {
            $user = Auth::guard('guest')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Check if user has active booking
            $currentDate = Carbon::now()->toDateString();
            $activeBooking = Booking::where('guest_id', $user->id)
                ->where('booking_status', 'confirmed')
                ->where('check_in_date', '<=', $currentDate)
                ->where('check_out_date', '>=', $currentDate)
                ->with('room')
                ->first();
            
            if (!$activeBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active booking found'
                ], 403);
            }
            
            // Get room service categories with items - NE PAS LIMITER LES CHAMPS
            // Cela permet d'obtenir toutes les données dont vous avez besoin
            $categories = RoomServiceCategory::with(['items' => function($query) {
                $query->where('is_available', true);
            }])->get();
            
            // Formater les données pour inclure l'URL proxy si nécessaire
            $formattedCategories = $categories->map(function($category) {
                $formattedItems = $category->items->map(function($item) {
                    // Ajouter l'URL proxy si l'image_url est une URL S3
                    $itemData = $item->toArray();
                    
                    if (!empty($item->image_url) && strpos($item->image_url, 'staynest-images.s3.eu-central-2.idrivee2.com/') !== false) {
                        $urlParts = explode('/', $item->image_url);
                        $filename = end($urlParts);
                        $itemData['image_proxy_url'] = url("/api/room-service-images/{$filename}");
                    } else {
                        $itemData['image_proxy_url'] = $item->image_url;
                    }
                    
                    return $itemData;
                });
                
                return [
                    'id' => $category->id,
                    'name_en' => $category->name_en,
                    'name_ar' => $category->name_ar,
                    'description' => $category->description,
                    'items' => $formattedItems
                ];
            });
            
            return response()->json([
                'success' => true,
                'categories' => $formattedCategories,
                'booking' => [
                    'id' => $activeBooking->id,
                    'room_number' => $activeBooking->room->room_number ?? 'N/A'
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error retrieving room service menu: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving menu: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * Place a room service order
     */
    public function placeOrder(Request $request)
    {
        try {
            $user = Auth::guard('guest')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Validate request
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'items' => 'required|array',
                'items.*.item_id' => 'required|exists:room_service_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'special_instructions' => 'nullable|string'
            ]);
            
            // Verify the booking belongs to the user and is active
            $currentDate = Carbon::now()->toDateString();
            $booking = Booking::where('id', $validated['booking_id'])
                ->where('guest_id', $user->id)
                ->where('booking_status', 'confirmed')
                ->where('check_in_date', '<=', $currentDate)
                ->where('check_out_date', '>=', $currentDate)
                ->first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or inactive booking'
                ], 403);
            }
            
            // Calculate total price
            $totalPrice = 0;
            $orderItems = [];
            
            foreach ($validated['items'] as $item) {
                $menuItem = RoomServiceItem::find($item['item_id']);
                
                if (!$menuItem) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Item not found'
                    ], 404);
                }
                
                if (!$menuItem->is_available) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Item ' . $menuItem->name_en . ' is not available'
                    ], 400);
                }
                
                $itemTotal = $menuItem->price * $item['quantity'];
                $totalPrice += $itemTotal;
                
                $orderItems[] = [
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->price,
                    'notes' => $item['notes'] ?? null
                ];
            }
            
            // Add delivery charge
            $deliveryCharge = 5.00;
            $totalPrice += $deliveryCharge;
            
            // Create room service order
            $order = RoomServiceOrder::create([
                'booking_id' => $booking->id,
                'room_id' => $booking->room_id,
                'guest_id' => $user->id,
                'order_date' => now(),
                'status' => 'pending',
                'special_instructions' => $validated['special_instructions'] ?? null,
                'total_price' => $totalPrice,
                'delivery_charge' => $deliveryCharge,
                'expected_delivery_time' => now()->addMinutes(30)
            ]);
            
            // Create order items
            foreach ($orderItems as $orderItem) {
                $order->items()->create($orderItem);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => 'RS' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'total_price' => $totalPrice,
                    'expected_delivery_time' => $order->expected_delivery_time
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error placing order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getOrderHistory(Request $request)
    {
        try {
            $user = Auth::guard('guest')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $orders = RoomServiceOrder::with(['items', 'items.item'])
                ->where('guest_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error retrieving order history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving order history: ' . $e->getMessage()
            ], 500);
        }
    }
}