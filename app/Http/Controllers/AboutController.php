<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HomePage;
use App\Models\Room;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function getHomePageData()
    {
        try {
            $homePage = HomePage::first();
            
            return response()->json([
                'success' => true,
                'data' => $homePage ?: []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch home page data'
            ], 500);
        }
    }

    public function getRoomsCount()
    {
        try {
            $totalRooms = Room::count();
            $availableRooms = Room::where('status', 'available')->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_rooms' => $totalRooms,
                    'available_rooms' => $availableRooms
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rooms count'
            ], 500);
        }
    }

    public function getGuestsCount()
    {
        try {
            $totalGuests = Guest::count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_guests' => $totalGuests
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch guests count'
            ], 500);
        }
    }

    public function getStaffCount()
    {
        try {
            $totalStaff = User::where('is_active', true)->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_staff' => $totalStaff
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff count'
            ], 500);
        }
    }
}