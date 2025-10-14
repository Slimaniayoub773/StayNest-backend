<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RoomServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomServiceCategoryController extends Controller
{
   public function index()
    {
        try {
            $categories = RoomServiceCategory::all();
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories'
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_food' => 'boolean',
            'available_hours' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = RoomServiceCategory::create($request->all());

        return response()->json(['data' => $category], 201);
    }

    public function show(RoomServiceCategory $roomServiceCategory)
    {
        return response()->json(['data' => $roomServiceCategory], 200);
    }

    public function update(Request $request, RoomServiceCategory $roomServiceCategory)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_food' => 'boolean',
            'available_hours' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $roomServiceCategory->update($request->all());

        return response()->json(['data' => $roomServiceCategory], 200);
    }

    public function destroy(RoomServiceCategory $roomServiceCategory)
    {
        $roomServiceCategory->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
