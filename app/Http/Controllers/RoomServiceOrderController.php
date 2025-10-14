<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RoomServiceOrder;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomServiceOrderController extends Controller
{
    public function index(Request $request)
{
    $query = RoomServiceOrder::with(['booking', 'room', 'guest'])
        ->orderBy('order_date', 'desc');

    // Filter by status
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    // Search functionality
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('guest', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('room', function($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%");
            })->orWhere('special_instructions', 'like', "%{$search}%");
        });
    }

    $orders = $query->paginate($request->per_page ?? 15);

    return response()->json([
        'success' => true,
        'data' => $orders->items(), // Return just the array of items
        'meta' => [
            'total' => $orders->total(),
            'per_page' => $orders->perPage(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
        ]
    ]);
}

    public function create()
    {
        $bookings = Booking::active()->with(['room', 'guest'])->get();
        $guests = Guest::all();
        $rooms = Room::all();

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings,
                'guests' => $guests,
                'rooms' => $rooms
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'status' => 'required|in:pending,processing,delivered,cancelled',
            'special_instructions' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
            'delivery_charge' => 'nullable|numeric|min:0',
            'expected_delivery_time' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order = RoomServiceOrder::create(array_merge(
            $request->all(),
            ['order_date' => now()]
        ));

        return response()->json([
            'success' => true,
            'data' => $order->load(['booking', 'room', 'guest'])
        ], 201);
    }

    public function show($id)
    {
        $order = RoomServiceOrder::with(['booking', 'room', 'guest'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function update(Request $request, $id)
    {
        $order = RoomServiceOrder::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:pending,processing,delivered,cancelled',
            'special_instructions' => 'nullable|string',
            'delivery_charge' => 'nullable|numeric|min:0',
            'expected_delivery_time' => 'nullable|date_format:H:i',
            'actual_delivery_time' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $order->load(['booking', 'room', 'guest'])
        ]);
    }

    public function destroy($id)
    {
        $order = RoomServiceOrder::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }

    public function getByStatus($status)
    {
        $orders = RoomServiceOrder::with(['booking', 'room', 'guest'])
            ->where('status', $status)
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function getByGuest($guestId)
    {
        $orders = RoomServiceOrder::with(['booking', 'room', 'guest'])
            ->where('guest_id', $guestId)
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function getByRoom($roomId)
    {
        $orders = RoomServiceOrder::with(['booking', 'room', 'guest'])
            ->where('room_id', $roomId)
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}