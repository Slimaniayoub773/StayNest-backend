<?php
namespace App\Http\Controllers;

    use App\Models\Guest;
    use App\Models\OtpVerification;
    use App\Models\Role;
    use App\Models\User;
    use App\Notifications\GuestRegisteredNotification;
    use App\Notifications\OtpNotification;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Validation\ValidationException;
    use Google\Client;
    use Illuminate\Support\Str;

    class GuestAuthController extends Controller
    {
        /**
         * Register a new guest
         */
    public function register(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:guests',
                    'phone' => 'required|string|max:20',
                    'identification_number' => 'required|string|max:50|unique:guests',
                    'password' => 'required|string|min:8|confirmed',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                // Create guest (but not verified yet)
                $guest = Guest::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'identification_number' => $request->identification_number,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => null, // Not verified yet
                ]);

                Log::info('Guest created', ['guest_id' => $guest->id, 'email' => $guest->email]);

                // Generate and send OTP
                $otp = $this->generateAndSendOtp($guest);

                return response()->json([
                    'message' => 'Guest registered successfully. Please verify your email with OTP.',
                    'guest_id' => $guest->id,
                    'email' => $guest->email,
                    'requires_otp_verification' => true
                ], 201);

            } catch (\Exception $e) {
                Log::error('Guest Registration Error: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Generate and send OTP
         */
        private function generateAndSendOtp(Guest $guest)
        {
            try {
                // Generate 6-digit OTP
                $otp = rand(100000, 999999);
                $expiresAt = Carbon::now()->addMinutes(10); // OTP valid for 10 minutes

                // Delete any existing OTPs for this guest
                OtpVerification::where('guest_id', $guest->id)->delete();

                // Create new OTP record
                $otpRecord = OtpVerification::create([
                    'guest_id' => $guest->id,
                    'otp' => $otp,
                    'expires_at' => $expiresAt,
                ]);

                // Send OTP via email
                $guest->notify(new OtpNotification($otp, $guest->name));

                Log::info('OTP sent to guest', [
                    'guest_id' => $guest->id,
                    'email' => $guest->email,
                    'otp_id' => $otpRecord->id
                ]);

                return $otp;

            } catch (\Exception $e) {
                Log::error('OTP Generation Error: ' . $e->getMessage());
                throw $e;
            }
        }

        /**
         * Verify OTP
         */
        public function verifyOtp(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'guest_id' => 'required|exists:guests,id',
                    'otp' => 'required|string|size:6',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $otpRecord = OtpVerification::where('guest_id', $request->guest_id)
                    ->where('otp', $request->otp)
                    ->first();

                if (!$otpRecord) {
                    return response()->json([
                        'message' => 'Invalid OTP',
                        'errors' => ['otp' => ['The OTP code is invalid.']]
                    ], 422);
                }

                if (Carbon::now()->gt($otpRecord->expires_at)) {
                    return response()->json([
                        'message' => 'OTP expired',
                        'errors' => ['otp' => ['The OTP has expired. Please request a new one.']]
                    ], 422);
                }

                // OTP is valid, verify the guest
                $guest = Guest::find($request->guest_id);
                $guest->update([
                    'email_verified_at' => Carbon::now()
                ]);

                // Delete the used OTP
                $otpRecord->delete();

                // Send notifications to admins
                $this->sendGuestRegistrationNotification($guest);

                // Create token
                $token = $guest->createToken('guestAuthToken')->plainTextToken;

                return response()->json([
                    'message' => 'Email verified successfully!',
                    'guest' => $guest,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ], 200);

            } catch (\Exception $e) {
                Log::error('OTP Verification Error: ' . $e->getMessage());
                return response()->json([
                    'message' => 'OTP verification failed',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Resend OTP
         */
        public function resendOtp(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'guest_id' => 'required|exists:guests,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $guest = Guest::find($request->guest_id);
                
                if ($guest->email_verified_at) {
                    return response()->json([
                        'message' => 'Email already verified',
                        'errors' => ['email' => ['Email is already verified.']]
                    ], 422);
                }

                // Generate and send new OTP
                $this->generateAndSendOtp($guest);

                return response()->json([
                    'message' => 'OTP sent successfully to your email.',
                ], 200);

            } catch (\Exception $e) {
                Log::error('Resend OTP Error: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Failed to resend OTP',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        // ... rest of your existing methods (login, logout, googleAuth, etc.)
        
        /**
         * Send guest registration notification to admins
         */
        private function sendGuestRegistrationNotification(Guest $guest)
        {
            try {
                $adminRole = Role::where('role_name', 'admin')->first();
                
                if (!$adminRole) {
                    Log::warning('Admin role not found when sending guest registration notification');
                    return;
                }

                $adminUsers = User::where('role_id', $adminRole->id)->get();

                if ($adminUsers->isEmpty()) {
                    Log::warning('No admin users found for guest registration notification');
                    return;
                }

                $notificationCount = 0;
                foreach ($adminUsers as $admin) {
                    $admin->notify(new GuestRegisteredNotification($guest));
                    $notificationCount++;
                }

                Log::info('Guest registration notifications sent', [
                    'guest_id' => $guest->id,
                    'admin_count' => $adminUsers->count(),
                    'notifications_sent' => $notificationCount
                ]);

            } catch (\Exception $e) {
                Log::error('Guest Registration Notification Error: ' . $e->getMessage(), [
                    'guest_id' => $guest->id,
                    'error_trace' => $e->getTraceAsString()
                ]);
            }
        }
        /**
         * Login guest
         */
        public function login(Request $request)
        {
            try {
                $request->validate([
                    'email' => 'required|email',
                    'password' => 'required',
                ]);

                $guest = Guest::where('email', $request->email)->first();

                // Check if this is a Google-authenticated user with no password
                if ($guest && is_null($guest->password)) {
                    return response()->json([
                        'message' => 'Google account',
                        'errors' => [
                            'email' => ['This account was created with Google. Please use Google Sign-In.']
                        ]
                    ], 401);
                }

                if (!$guest || !Hash::check($request->password, $guest->password)) {
                    return response()->json([
                        'message' => 'Invalid credentials',
                        'errors' => [
                            'email' => ['The provided credentials are incorrect.']
                        ]
                    ], 401);
                }

                if ($guest->is_blocked) {
                    return response()->json([
                        'message' => 'Account disabled',
                        'errors' => [
                            'email' => ['Your account has been disabled. Please contact support.']
                        ]
                    ], 403);
                }

                $token = $guest->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'Login successful',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'guest' => $guest,
                ]);

            } catch (ValidationException $e) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                Log::error('Guest Login Error: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Authentication failed',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Logout guest (revoke token)
         */
        public function logout(Request $request)
        {
            try {
                $request->user()->currentAccessToken()->delete();

                return response()->json([
                    'message' => 'Successfully logged out'
                ]);

            } catch (\Exception $e) {
                Log::error('Guest Logout Error: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Logout failed',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Get authenticated guest details
         */
        public function guest(Request $request)
        {
            try {
                return response()->json([
                    'guest' => $request->user()
                ]);

            } catch (\Exception $e) {
                Log::error('Guest Fetch Error: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Unable to fetch guest data',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Google authentication for guests
         */
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

        // Check if guest already exists
        $guest = Guest::where('email', $payload['email'])->first();

        $isNewGuest = false;
        $requiresPassword = false;

        if (!$guest) {
            // Create new guest with null password for Google signups
            $guest = Guest::create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'password' => null, // Set to null for Google signups
                'google_id' => $payload['sub'],
                'is_blocked' => false,
                'email_verified_at' => now(),
                'phone' => '000-000-0000', // Temporary placeholder
                'identification_number' => 'GOOGLE_' . $payload['sub'] . '_' . time(), // More unique ID
            ]);
            
            $isNewGuest = true;
            $requiresPassword = true; // New Google users need to set password
        } else {
            // Check if existing guest needs to set password
            if (empty($guest->password) || is_null($guest->password)) {
                $requiresPassword = true;
            }
            
            // Update Google ID if not set
            if (empty($guest->google_id)) {
                $guest->google_id = $payload['sub'];
                $guest->save();
            }
        }

        // Send notification for new guest registration via Google
        if ($isNewGuest) {
            $this->sendGuestRegistrationNotification($guest);
        }

        // Create token
        $token = $guest->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'message' => 'Google authentication successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'guest' => $guest,
            'requires_password' => $requiresPassword
        ]);

    } catch (\Exception $e) {
        Log::error('Guest Google Auth Error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Google authentication failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
/**
 * Set password for Google-authenticated users
 */
public function setPassword(Request $request)
{
    try {
        \Log::info('Set Password Request Received', [
            'user' => $request->user() ? $request->user()->id : 'null',
            'has_token' => $request->bearerToken() ? 'yes' : 'no',
        ]);

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $guest = $request->user();
        
        if (!$guest) {
            \Log::warning('User not authenticated in setPassword');
            return response()->json([
                'message' => 'User not authenticated',
            ], 401);
        }

        // Update password
        $guest->password = Hash::make($request->password);
        $guest->save();

        \Log::info('Password set successfully for guest', ['guest_id' => $guest->id]);

        return response()->json([
            'message' => 'Password set successfully',
        ], 200);
        
    } catch (\Exception $e) {
        \Log::error('Set Password Error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to set password',
            'error' => $e->getMessage()
        ], 500);    
    }
}
    }