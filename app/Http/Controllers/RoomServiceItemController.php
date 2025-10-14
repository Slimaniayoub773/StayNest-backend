<?php

namespace App\Http\Controllers;

use App\Models\RoomServiceItem;
use App\Models\RoomServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RoomServiceItemController extends Controller
{
    public function index()
{
    try {
        $items = RoomServiceItem::with('category')->get();

        // تحويل image_url إلى رابط كامل للمتصفح
        $items->transform(function ($item) {
            if ($item->image_url) {
                $item->image_url = asset($item->image_url);
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch items',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function store(Request $request)
    {
        Log::info('=== STORE ITEM REQUEST ===');
        Log::info('Request data:', $request->all());
        Log::info('Files:', ['has_image' => $request->hasFile('image')]);

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:room_service_categories,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'preparation_time' => 'required|integer|min:1',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'category_id.required' => 'Please select a category',
            'category_id.exists' => 'The selected category does not exist',
            'name_ar.required' => 'Arabic name is required',
            'name_en.required' => 'English name is required',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price must be at least 0',
            'preparation_time.required' => 'Preparation time is required',
            'preparation_time.integer' => 'Preparation time must be a whole number',
            'preparation_time.min' => 'Preparation time must be at least 1 minute',
            'is_available.required' => 'Availability status is required',
            'is_available.boolean' => 'Availability must be true or false',
            'image.image' => 'The file must be an image',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp',
            'image.max' => 'The image may not be greater than 5MB',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'category_id' => $request->category_id,
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'description' => $request->description ?? '',
                'price' => (float) $request->price,
                'preparation_time' => (int) $request->preparation_time,
                'is_available' => filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN),
            ];

            Log::info('Processed data for creation:', $data);

            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
    $image = $request->file('image');
    $filename = 'item_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
    
    // يخزن الصورة في storage/app/public/room-service-items
    $path = $image->storeAs('room-service-items', $filename, 'public');
    
    // الرابط الكامل للعرض في المتصفح
    $data['image_url'] = asset("storage/$path");
}

            $item = RoomServiceItem::create($data);
            $item->load('category');

            Log::info('Item created successfully:', [
                'id' => $item->id,
                'name' => $item->name_en,
                'category' => $item->category ? $item->category->name_en : 'NULL',
                'image_url' => $item->image_url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item created successfully',
                'data' => $item
            ], 201);

        } catch (\Exception $e) {
            Log::error('Item creation failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, RoomServiceItem $roomServiceItem)
    {
        Log::info('=== UPDATE ITEM REQUEST ===');
        Log::info('Item ID:', ['id' => $roomServiceItem->id]);
        Log::info('Request data:', $request->all());
        Log::info('Files:', ['has_image' => $request->hasFile('image')]);

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:room_service_categories,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'preparation_time' => 'required|integer|min:1',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url' => 'nullable|string' // For keeping existing image
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'category_id' => $request->category_id,
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'description' => $request->description ?? '',
                'price' => (float) $request->price,
                'preparation_time' => (int) $request->preparation_time,
                'is_available' => filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN),
            ];

            Log::info('Processed data for update:', $data);

            // Handle image upload/update
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
    // حذف الصورة القديمة إذا موجودة
    if ($roomServiceItem->image_url) {
        $oldPath = str_replace(asset('storage/'), '', $roomServiceItem->image_url);
        Storage::disk('public')->delete($oldPath);
    }

    $image = $request->file('image');
    $filename = 'item_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
    $path = $image->storeAs('room-service-items', $filename, 'public');
    
    $data['image_url'] = asset("storage/$path");
}


            $roomServiceItem->update($data);
            $roomServiceItem->load('category');

            Log::info('Item updated successfully:', [
                'id' => $roomServiceItem->id,
                'name' => $roomServiceItem->name_en,
                'category' => $roomServiceItem->category ? $roomServiceItem->category->name_en : 'NULL',
                'image_url' => $roomServiceItem->image_url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $roomServiceItem
            ]);

        } catch (\Exception $e) {
            Log::error('Item update failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RoomServiceItem $roomServiceItem)
    {
        try {
            Log::info('Deleting item:', ['id' => $roomServiceItem->id, 'name' => $roomServiceItem->name_en]);

            // Delete associated image if exists
            if ($roomServiceItem->image_url) {
                $imagePath = str_replace(Storage::disk('public')->url(''), '', $roomServiceItem->image_url);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                    Log::info('Image deleted:', ['path' => $imagePath]);
                }
            }

            $roomServiceItem->delete();

            Log::info('Item deleted successfully');

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Item deletion failed:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function debugImages()
{
    $items = RoomServiceItem::all();
    
    foreach ($items as $item) {
        echo "Item: {$item->name_en}<br>";
        echo "Stored image_url: {$item->image_url}<br>";
        
        if ($item->image_url) {
            // Check if it's a full URL
            if (filter_var($item->image_url, FILTER_VALIDATE_URL)) {
                echo "✓ Is full URL<br>";
            } else {
                echo "✗ Is relative path<br>";
            }
            
            // Try to generate URL
            $generatedUrl = Storage::disk('public')->url($item->image_url);
            echo "Generated URL: {$generatedUrl}<br>";
            
            // Check if file exists
            $filePath = str_replace(Storage::disk('public')->url(''), '', $item->image_url);
            $exists = Storage::disk('public')->exists($filePath);
            echo "File exists: " . ($exists ? 'YES' : 'NO') . "<br>";
        }
        echo "<hr>";
    }
}
}