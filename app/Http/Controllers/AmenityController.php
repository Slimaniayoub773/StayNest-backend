<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index()
    {
        return Amenity::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_class' => 'nullable|string|max:255'
        ]);

        $amenity = Amenity::create($data);

        return response()->json($amenity, 201);
    }

    public function update(Request $request, Amenity $amenity)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'icon_class' => 'nullable|string|max:255'
        ]);

        $amenity->update($data);

        return response()->json($amenity);
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->delete();
        return response()->json(null, 204);
    }
}