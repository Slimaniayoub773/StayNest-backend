<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::all();
        return response()->json(['data' => $guests], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:guests,email',
            'phone' => 'nullable|string|max:20',
            'identification_number' => 'nullable|string|max:255',
            'is_blocked' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $guest = Guest::create($request->all());

        return response()->json(['data' => $guest, 'message' => 'Guest created successfully'], 201);
    }

    public function show(Guest $guest)
    {
        return response()->json(['data' => $guest], 200);
    }

    public function update(Request $request, Guest $guest)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('guests')->ignore($guest->id),
            ],
            'phone' => 'nullable|string|max:20',
            'identification_number' => 'nullable|string|max:255',
            'is_blocked' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $guest->update($request->all());

        return response()->json(['data' => $guest, 'message' => 'Guest updated successfully'], 200);
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        return response()->json(['message' => 'Guest deleted successfully'], 200);
    }

    public function toggleBlock(Guest $guest)
    {
        $guest->update(['is_blocked' => !$guest->is_blocked]);
        
        $status = $guest->is_blocked ? 'blocked' : 'unblocked';
        return response()->json([
            'data' => $guest, 
            'message' => "Guest {$status} successfully"
        ], 200);
    }
}