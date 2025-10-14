<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RoomAmenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomAmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomAmenities = RoomAmenity::with(['room', 'amenity'])->get();
        return response()->json([
            'success' => true,
            'data' => $roomAmenities
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'amenity_id' => 'required|exists:amenities,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if this assignment already exists
        $exists = RoomAmenity::where('room_id', $request->room_id)
                            ->where('amenity_id', $request->amenity_id)
                            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This amenity is already assigned to this room'
            ], 409);
        }

        $roomAmenity = RoomAmenity::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Room amenity assigned successfully',
            'data' => $roomAmenity->load(['room', 'amenity'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomAmenity $roomAmenity)
    {
        return response()->json([
            'success' => true,
            'data' => $roomAmenity->load(['room', 'amenity'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoomAmenity $roomAmenity)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'amenity_id' => 'required|exists:amenities,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if this assignment already exists for another record
        $exists = RoomAmenity::where('room_id', $request->room_id)
                            ->where('amenity_id', $request->amenity_id)
                            ->where('id', '!=', $roomAmenity->id)
                            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This amenity is already assigned to this room'
            ], 409);
        }

        $roomAmenity->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Room amenity updated successfully',
            'data' => $roomAmenity->load(['room', 'amenity'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomAmenity $roomAmenity)
    {
        $roomAmenity->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Room amenity deleted successfully'
        ]);
    }
}