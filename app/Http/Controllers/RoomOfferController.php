<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Offer;
use App\Models\RoomOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomOfferController extends Controller
{
    public function index()
    {
        $roomOffers = RoomOffer::with(['room', 'offer'])->get();
        return response()->json(['data' => $roomOffers], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'offer_id' => 'required|exists:offers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if relationship already exists
        $exists = RoomOffer::where('room_id', $request->room_id)
                          ->where('offer_id', $request->offer_id)
                          ->exists();

        if ($exists) {
            return response()->json(['message' => 'This room-offer relationship already exists'], 409);
        }

        $roomOffer = RoomOffer::create($request->all());

        return response()->json([
            'data' => $roomOffer->load(['room', 'offer']),
            'message' => 'Room-Offer relationship created successfully'
        ], 201);
    }

    public function destroy(RoomOffer $roomOffer)
    {
        $roomOffer->delete();
        return response()->json(['message' => 'Room-Offer relationship deleted successfully'], 200);
    }

    public function getAvailableRooms($offerId)
    {
        $rooms = Room::whereDoesntHave('offers', function($query) use ($offerId) {
            $query->where('offer_id', $offerId);
        })->get();

        return response()->json(['data' => $rooms], 200);
    }

    public function getAvailableOffers($roomId)
    {
        $offers = Offer::whereDoesntHave('rooms', function($query) use ($roomId) {
            $query->where('room_id', $roomId);
        })->get();

        return response()->json(['data' => $offers], 200);
    }
}