<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CleaningSchedule;
use App\Models\Room;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CleaningScheduleController extends Controller
{
    // In your CleaningScheduleController
public function index()
{
    return response()->json([
        'data' => CleaningSchedule::with(['room', 'cleaner'])->get(),
        'message' => 'Cleaning schedules retrieved successfully'
    ]);
}
public function getCleaners()
{
    try {
        // First, check if the 'cleaner' role exists in the database
        $cleanerRole = Role::where('role_name', 'Cleaner')->first();

        if (!$cleanerRole) {
            return response()->json([
                'error' => 'Configuration missing',
                'message' => 'Cleaner role does not exist in database'
            ], 404);
        }

        // Get users with the cleaner role
        $cleaners = User::where('role_id', $cleanerRole->id)
                      ->select('id', 'name', 'email') // Only select necessary fields
                      ->get();

        if ($cleaners->isEmpty()) {
            return response()->json([
                'error' => 'No cleaners found',
                'message' => 'No users are assigned the cleaner role'
            ], 404);
        }

        return response()->json([
            'data' => $cleaners,
            'message' => 'Cleaners retrieved successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Server error',
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'room_id' => 'required|exists:rooms,id',
        'cleaner_id' => 'required|exists:users,id',
        'cleaning_date' => 'required|date',
        'cleaning_status' => 'sometimes|in:pending,in_progress,completed,skipped',
        'priority_level' => 'sometimes|in:low,normal,high',
        'notes' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    $schedule = CleaningSchedule::create($request->all());

    // رجّع مع العلاقات
    $schedule->load(['room', 'cleaner']);

    return response()->json([
        'data' => $schedule,
        'message' => 'Cleaning schedule created successfully'
    ], 201);
}

public function update(Request $request, CleaningSchedule $cleaningSchedule)
{
    $validator = Validator::make($request->all(), [
        'room_id' => 'sometimes|exists:rooms,id',
        'cleaner_id' => 'sometimes|exists:users,id',
        'cleaning_date' => 'sometimes|date',
        'cleaning_status' => 'sometimes|in:pending,in_progress,completed,skipped',
        'priority_level' => 'sometimes|in:low,normal,high',
        'notes' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    $cleaningSchedule->update($request->all());

    // رجّع مع العلاقات
    $cleaningSchedule->load(['room', 'cleaner']);

    return response()->json([
        'data' => $cleaningSchedule,
        'message' => 'Cleaning schedule updated successfully'
    ]);
}

    public function show(CleaningSchedule $cleaningSchedule)
    {
        return response()->json([
            'data' => $cleaningSchedule->load(['room', 'cleaner']),
            'message' => 'Cleaning schedule retrieved successfully'
        ]);
    }

    public function destroy(CleaningSchedule $cleaningSchedule)
    {
        $cleaningSchedule->delete();

        return response()->json([
            'message' => 'Cleaning schedule deleted successfully'
        ]);
    }


}