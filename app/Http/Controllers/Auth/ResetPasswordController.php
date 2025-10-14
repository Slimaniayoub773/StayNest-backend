<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        try {
            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // ✅ Use guests broker
            $response = Password::broker('guests')->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($guest, $password) {
                    $guest->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($guest));
                }
            );

            return $response == Password::PASSWORD_RESET
                ? response()->json(['message' => 'Password reset successfully'], 200)
                : response()->json([
                    'message' => 'Unable to reset password',
                    'errors' => ['email' => [trans($response)]]
                ], 400);

        } catch (\Exception $e) {
            Log::error('Guest Password Reset Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
