<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomImageController extends Controller
{
    public function index($roomId)
    {
        $room = Room::findOrFail($roomId);
        $images = $room->images()->orderBy('is_primary', 'desc')->get();
        
        // Transform image URLs to full URLs
        $images->transform(function ($image) {
            $image->image_url = asset($image->image_url);
            return $image;
        });
        
        return response()->json([
            'success' => true,
            'data' => $images
        ]);
    }

    public function store(Request $request, $roomId)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10 MB
        'is_primary' => 'sometimes|boolean'
    ]);

    $room = Room::findOrFail($roomId);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('room_images', 'public');
        $url = asset("storage/$path"); // Use asset() helper for proper URL

        if ($request->input('is_primary', false)) {
            RoomImage::where('room_id', $roomId)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }

        $image = RoomImage::create([
            'room_id' => $roomId,
            'image_url' => $url,
            'is_primary' => $request->input('is_primary', false)
        ]);

        return response()->json([
            'success' => true,
            'data' => $image,
            'message' => 'Image uploaded successfully'
        ], 201);
    }

    return response()->json([
        'success' => false,
        'message' => 'No image file provided'
    ], 400);
}
    public function setPrimary($roomId, $imageId)
{
    $room = Room::findOrFail($roomId);
    $image = RoomImage::where('room_id', $roomId)->findOrFail($imageId);

    // Unset any existing primary image
    RoomImage::where('room_id', $roomId)
        ->where('is_primary', true)
        ->update(['is_primary' => false]);

    // Set this image as primary
    $image->update(['is_primary' => true]);

    return response()->json([
        'success' => true,
        'data' => $image,
        'message' => 'Primary image updated successfully'
    ]);
}
 public function destroy($roomId, $imageId)
{
    $room = Room::findOrFail($roomId);
    $image = RoomImage::where('room_id', $roomId)->findOrFail($imageId);

    // Delete the file from storage
    $path = str_replace('/storage', 'public', parse_url($image->image_url, PHP_URL_PATH));
    Storage::delete($path);

    // Delete the record
    $image->delete();

    // If this was the primary image, set a new primary if available
    if ($image->is_primary) {
        $newPrimary = RoomImage::where('room_id', $roomId)->first();
        if ($newPrimary) {
            $newPrimary->update(['is_primary' => true]);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Image deleted successfully'
    ]);
}}