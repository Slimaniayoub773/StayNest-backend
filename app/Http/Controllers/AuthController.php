<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Google\Client;
use Google\Service\Oauth2;
use Illuminate\Support\Str;

use App\Notifications\UserRegisteredNotification; // Add this
class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::firstOrCreate(
            ['role_name' => 'guest'],
            ['description' => 'Default guest role']
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role_id' => $role->id,
            'is_active' => true
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Send notification to admin about new user registration
        $adminUsers = User::whereHas('role', function($query) {
            $query->whereIn('role_name', ['Admin', 'Manager']);
        })->get();

        
        foreach ($adminUsers as $admin) {
            $admin->notify(new UserRegisteredNotification($user));
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);

    } catch (\Exception $e) {
        Log::error('Registration Error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Login user
     */
    // In your AuthController login method
public function login(Request $request)
{
    try {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->with('role')->first();
        $user = $user->load('role');
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Account disabled',
                'errors' => [
                    'email' => ['Your account has been disabled. Please contact support.']
                ]
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'role' => $user->role->role_name ?? null
        ]);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Login Error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Authentication failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Successfully logged out'
            ]);

        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user details
     */
    public function user(Request $request)
    {
        try {
            return response()->json([
                'user' => $request->user()->load('role')
            ]);

        } catch (\Exception $e) {
            Log::error('User Fetch Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Unable to fetch user data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

// Add this new method to your AuthController
    public function googleAuth(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'credential' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $client = new Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->credential);

        if (!$payload) {
            return response()->json([
                'message' => 'Invalid Google token',
                'errors' => ['credential' => ['Invalid Google authentication token.']]
            ], 401);
        }

        // Check if user already exists
        $user = User::where('email', $payload['email'])->first();
        $user = $user->load('role');

        if (!$user) {
            // Create new user
            $role = Role::firstOrCreate(
                ['role_name' => 'guest'],
                ['description' => 'Default guest role']
            );

            $user = User::create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'password' => Hash::make(Str::random(24)), // Random password since using Google auth
                'google_id' => $payload['sub'],
                'role_id' => $role->id,
                'is_active' => true,
                'email_verified_at' => now(), // Google already verified the email
            ]);
        }

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
    'message' => 'Google authentication successful',
    'access_token' => $token,
    'token_type' => 'Bearer',
    'user' => $user->load('role'),
    'role' => $user->role->role_name ?? null
]);


    } catch (\Exception $e) {
        Log::error('Google Auth Error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Google authentication failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
}