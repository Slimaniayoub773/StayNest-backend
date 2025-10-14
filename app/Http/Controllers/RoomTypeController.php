<?php

namespace App\Http\Controllers;     

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;    
use Illuminate\Support\Facades\Validator;

class RoomTypeController extends Controller
{
    /**
     * Get all room types
     */
    public function index()
    {
        $roomTypes = RoomType::all();
        return response()->json([
            'success' => true,
            'data' => $roomTypes
        ]);
    }

    /**
     * Get a specific room type
     */
    public function show($id)
    {
        $roomType = RoomType::find($id);

        if (!$roomType) {
            return response()->json([
                'success' => false,
                'message' => 'Room type not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $roomType
        ]);
    }

    /**
     * Create a new room type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:room_types',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $roomType = RoomType::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $roomType
        ], 201);
    }

    /**
     * Update a room type
     */
    public function update(Request $request, $id)
    {
        $roomType = RoomType::find($id);

        if (!$roomType) {
            return response()->json([
                'success' => false,
                'message' => 'Room type not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:room_types,name,' . $id,
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $roomType->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $roomType
        ]);
    }

    /**
     * Delete a room type
     */
    public function destroy($id)
    {
        $roomType = RoomType::find($id);

        if (!$roomType) {
            return response()->json([
                'success' => false,
                'message' => 'Room type not found'
            ], 404);
        }

        $roomType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room type deleted successfully'
        ]);
    }

    /**
     * Get room types with availability
     */
    public function available(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $roomTypes = RoomType::where('max_occupancy', '>=', $request->guests)
            ->with(['rooms' => function($query) use ($request) {
                $query->whereDoesntHave('bookings', function($q) use ($request) {
                    $q->where('check_out', '>', $request->check_in)
                      ->where('check_in', '<', $request->check_out);
                });
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $roomTypes
        ]);
    }
}