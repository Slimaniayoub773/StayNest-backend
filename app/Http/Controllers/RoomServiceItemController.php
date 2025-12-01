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

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:room_service_categories,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'preparation_time' => 'required|integer|min:1',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
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

            // Handle image upload to S3
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                
                // Generate unique filename
                $filename = 'room-service-items/' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Store in S3
                $path = Storage::disk('s3')->put($filename, file_get_contents($image), 'public');
                
                // Get S3 URL
                $s3Url = Storage::disk('s3')->url($filename);
                
                // Store S3 URL in database
                $data['image_url'] = $s3Url;
                
                Log::info('Image uploaded to S3:', [
                    'filename' => $filename,
                    's3_url' => $s3Url
                ]);
            }

            $item = RoomServiceItem::create($data);
            $item->load('category');

            Log::info('Item created successfully:', [
                'id' => $item->id,
                'name' => $item->name_en,
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

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:room_service_categories,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'preparation_time' => 'required|integer|min:1',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
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

            // Handle image update
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image from S3 if exists
                if ($roomServiceItem->image_url) {
                    $oldFilename = $this->extractS3Filename($roomServiceItem->image_url);
                    if ($oldFilename) {
                        Storage::disk('s3')->delete($oldFilename);
                        Log::info('Old image deleted from S3:', ['filename' => $oldFilename]);
                    }
                }

                // Upload new image to S3
                $image = $request->file('image');
                $filename = 'room-service-items/' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                Storage::disk('s3')->put($filename, file_get_contents($image), 'public');
                $s3Url = Storage::disk('s3')->url($filename);
                
                $data['image_url'] = $s3Url;
                
                Log::info('New image uploaded to S3:', [
                    'filename' => $filename,
                    's3_url' => $s3Url
                ]);
            }

            $roomServiceItem->update($data);
            $roomServiceItem->load('category');

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
            // Delete image from S3 if exists
            if ($roomServiceItem->image_url) {
                $filename = $this->extractS3Filename($roomServiceItem->image_url);
                if ($filename) {
                    Storage::disk('s3')->delete($filename);
                    Log::info('Image deleted from S3:', ['filename' => $filename]);
                }
            }

            $roomServiceItem->delete();

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

    /**
     * Extract filename from S3 URL
     */
    private function extractS3Filename($url)
    {
        // Extract filename from S3 URL
        // URL format: https://staynest-images.s3.eu-central-2.idrivee2.com/room-service-items/filename.jpg
        if (strpos($url, 'staynest-images.s3.eu-central-2.idrivee2.com/') !== false) {
            return str_replace('https://staynest-images.s3.eu-central-2.idrivee2.com/', '', $url);
        }
        
        return null;
    }

    /**
     * Test S3 connection
     */
    public function testS3()
    {
        try {
            // Test S3 connection
            $testFile = 'test.txt';
            $content = 'Test content ' . date('Y-m-d H:i:s');
            
            Storage::disk('s3')->put($testFile, $content);
            
            $exists = Storage::disk('s3')->exists($testFile);
            $url = Storage::disk('s3')->url($testFile);
            
            return response()->json([
                'success' => true,
                'message' => 'S3 connection successful',
                'data' => [
                    'file_exists' => $exists,
                    'url' => $url,
                    'bucket' => config('filesystems.disks.s3.bucket'),
                    'region' => config('filesystems.disks.s3.region'),
                    'endpoint' => config('filesystems.disks.s3.endpoint')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'S3 connection failed',
                'error' => $e->getMessage(),
                'config' => [
                    'bucket' => config('filesystems.disks.s3.bucket'),
                    'region' => config('filesystems.disks.s3.region'),
                    'endpoint' => config('filesystems.disks.s3.endpoint')
                ]
            ], 500);
        }
    }
}