<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Guest;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        try {
            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if guest exists first
            $guest = Guest::where('email', $request->email)->first();

            if (!$guest) {
                // Return success even if email doesn't exist for security
                return response()->json([
                    'message' => 'If that email address exists in our system, we\'ve sent a password reset link to it.'
                ], 200);
            }

            // ✅ Use guests broker - this will trigger the custom notification
            $response = Password::broker('guests')->sendResetLink(
                $request->only('email')
            );

            Log::info('Password reset link attempt', [
                'email' => $request->email,
                'response' => $response,
                'guest_exists' => !!$guest
            ]);

            if ($response === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'Password reset link sent to your email'
                ], 200);
            }

            // For security, don't reveal if email doesn't exist
            return response()->json([
                'message' => 'If that email address exists in our system, we\'ve sent a password reset link to it.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Guest Password Reset Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}