<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json(['data' => $users]);
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role_id' => 'required|exists:roles,id',
        'phone' => 'nullable|string|max:20',
        'is_active' => 'boolean'
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $request->role_id,
        'phone' => $request->phone,
        'is_active' => $request->is_active ?? true,
        'is_blocked' => false
    ]);

    // Load the role relationship before returning
    $user->load('role');

    return response()->json(['data' => $user], 201);
}

// Similarly update the update method to return user with role
public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'password' => 'nullable|string|min:8',
        'role_id' => 'required|exists:roles,id',
        'phone' => 'nullable|string|max:20',
        'is_active' => 'boolean'
    ]);

    $updateData = [
        'name' => $request->name,
        'email' => $request->email,
        'role_id' => $request->role_id,
        'phone' => $request->phone,
        'is_active' => $request->is_active
    ];

    if ($request->password) {
        $updateData['password'] = Hash::make($request->password);
    }

    $user->update($updateData);

    // Load the role relationship before returning
    $user->load('role');

    return response()->json(['data' => $user]);
}
    public function show(User $user)
    {
        return response()->json(['data' => $user->load('role')]);
    }


    public function blockToggle(Request $request, User $user)
    {
        $request->validate([
            'is_blocked' => 'required|boolean'
        ]);

        $user->update(['is_blocked' => $request->is_blocked]);

        return response()->json(['data' => $user->fresh()->load('role')]);
    }
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'message' => 'User deleted successfully',
                'deleted_user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}