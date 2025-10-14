<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index()
    {
        $rooms = Room::with('type')->get();
        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|unique:rooms',
            'type_id' => 'required|exists:room_types,id',
            'floor_number' => 'required|integer',
            'price_per_night' => 'required|numeric',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:available,booked,maintenance',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $room = Room::create($request->all());
$room->load('type'); // <-- add this line

return response()->json([
    'success' => true,
    'data' => $room
], 201);
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        return response()->json([
            'success' => true,
            'data' => $room->load('type')
        ]);
    }

    /**
     * Update the specified room in storage.
     */
public function update(Request $request, Room $room)
{
    \Log::info('Update Request Data:', $request->all());
    \Log::info('Current Room:', $room->toArray());
    $validator = Validator::make($request->all(), [
    'room_number' => 'sometimes|string|unique:rooms,room_number,' . $room->id . ',id',
    'type_id' => 'sometimes|integer|exists:room_types,id',
    'floor_number' => 'sometimes|integer|min:0',
    'price_per_night' => 'sometimes|numeric|min:0',
    'description' => 'nullable|string',
    'status' => 'sometimes|in:available,booked,maintenance,cleaning',
]);

    if ($validator->fails()) {
        \Log::error('Validation Errors:', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $room->update($request->all());

    return response()->json([
        'success' => true,
        'data' => $room
    ]);
}
    /**
     * Remove the specified room from storage.
     */
    public function destroy(Room $room)
{
    \Log::info('Deleting room ID: '.$room->id);
    $room->delete(); // <-- this should remove it permanently

    return response()->json([
        'success' => true,
        'message' => 'Room deleted successfully'
    ]);
}

    /**
     * Get available rooms
     */
    public function available()
    {
        $rooms = Room::where('status', 'available')->with('type')->get();
        
        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }
}