<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\OtpVerification;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // -------------------------------------------------------------------------
    // REGISTRATION
    // -------------------------------------------------------------------------

    /**
     * Step 1 of registration — validate input, send OTP, do NOT create user yet.
     *
     * POST /api/auth/register
     * Body: name, email, password, password_confirmation, phone (optional)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone'    => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Generate OTP and store pending registration payload (password is hashed)
        ['model' => $otpRecord, 'plain_otp' => $plainOtp] = OtpVerification::generate(
            $request->email,
            'registration',
            [
                'name'     => $request->name,
                'password' => Hash::make($request->password),
                'phone'    => $request->phone,
            ]
        );

        // Send OTP email
        $sent = $this->sendOtpEmail($request->email, $plainOtp, 'registration', $request->name);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email address. Please verify to complete registration.',
            'data'    => [
                'needs_verification' => true,
                'email'              => $request->email,
                'otp_expires_at'     => $otpRecord->expires_at->toIso8601String(),
                'email_sent'         => $sent,
            ],
        ], 200);
    }

    /**
     * Step 2 of registration — verify OTP, create the user, return token.
     *
     * POST /api/auth/verify-email
     * Body: email, otp
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'otp'   => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $otpRecord = OtpVerification::findValid($request->email, 'registration');

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired or was not found. Please register again.',
                'errors'  => ['otp' => ['OTP is invalid or expired.']],
            ], 422);
        }

        if (!$otpRecord->verify($request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please check and try again.',
                'errors'  => ['otp' => ['The OTP you entered is incorrect.']],
            ], 422);
        }

        // Double-check the email isn't already taken (race condition guard)
        if (User::where('email', $request->email)->exists()) {
            $otpRecord->delete();
            return response()->json([
                'success' => false,
                'message' => 'This email is already registered. Please login.',
            ], 422);
        }

        // Create the user from the stored payload
        $payload = $otpRecord->payload;
        $user = User::create([
            'name'     => $payload['name'],
            'email'    => $request->email,
            'password' => $payload['password'], // already hashed
            'phone'    => $payload['phone'] ?? null,
            'role'     => 'customer',
        ]);

        // Delete the OTP record
        $otpRecord->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email verified. Registration complete!',
            'data'    => [
                'user' => [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'phone'          => $user->phone,
                    'role'           => $user->role,
                    'loyalty_points' => $user->loyalty_points,
                    'loyalty_tier'   => $user->loyalty_tier,
                ],
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    /**
     * Resend OTP for registration or forgot_password.
     *
     * POST /api/auth/resend-otp
     * Body: email, type (registration|forgot_password)
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'type'  => 'required|in:registration,forgot_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // For registration: email must NOT exist in users yet
        if ($request->type === 'registration') {
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already registered. Please login.',
                ], 422);
            }

            // Retrieve the previous payload so we don't lose it
            $existing = OtpVerification::where('email', $request->email)
                ->where('type', 'registration')
                ->first();
            $payload = $existing ? $existing->payload : null;

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending registration found for this email. Please register again.',
                ], 422);
            }

            ['model' => $otpRecord, 'plain_otp' => $plainOtp] = OtpVerification::generate(
                $request->email, 'registration', $payload
            );
            $name = $payload['name'] ?? 'there';
        } else {
            // For forgot_password: email must exist in users
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                // Generic response for security
                return response()->json([
                    'success' => true,
                    'message' => 'If your email exists, a new OTP has been sent.',
                ], 200);
            }

            ['model' => $otpRecord, 'plain_otp' => $plainOtp] = OtpVerification::generate(
                $request->email, 'forgot_password'
            );
            $name = $user->name;
        }

        $sent = $this->sendOtpEmail($request->email, $plainOtp, $request->type, $name);

        return response()->json([
            'success' => true,
            'message' => 'A new OTP has been sent to your email.',
            'data'    => [
                'email'          => $request->email,
                'otp_expires_at' => $otpRecord->expires_at->toIso8601String(),
                'email_sent'     => $sent,
            ],
        ], 200);
    }

    // -------------------------------------------------------------------------
    // LOGIN / LOGOUT / TOKEN
    // -------------------------------------------------------------------------

    /**
     * POST /api/auth/login
     * Body: email, password
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors'  => ['email' => ['The provided credentials are incorrect.']],
            ], 401);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data'    => [
                'user' => [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'phone'          => $user->phone,
                    'role'           => $user->role,
                    'address'        => $user->address,
                    'loyalty_points' => $user->loyalty_points,
                    'loyalty_tier'   => $user->loyalty_tier,
                    'avatar'         => $user->avatar,
                ],
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ], 200);
    }

    /**
     * POST /api/auth/logout-all
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out from all devices',
        ], 200);
    }

    /**
     * POST /api/auth/refresh-token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    // -------------------------------------------------------------------------
    // PROFILE
    // -------------------------------------------------------------------------

    /**
     * GET /api/user/profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'user' => [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'phone'          => $user->phone,
                    'role'           => $user->role,
                    'address'        => $user->address,
                    'loyalty_points' => $user->loyalty_points,
                    'loyalty_tier'   => $user->loyalty_tier,
                    'avatar'         => $user->avatar,
                    'created_at'     => $user->created_at,
                ],
            ],
        ], 200);
    }

    /**
     * PUT /api/user/profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'    => 'sometimes|string|max:255',
            'phone'   => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update($request->only(['name', 'phone', 'address']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data'    => [
                'user' => [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'phone'          => $user->phone,
                    'role'           => $user->role,
                    'address'        => $user->address,
                    'loyalty_points' => $user->loyalty_points,
                    'loyalty_tier'   => $user->loyalty_tier,
                    'avatar'         => $user->avatar,
                ],
            ],
        ], 200);
    }

    /**
     * POST /api/user/change-password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'errors'  => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ], 200);
    }

    // -------------------------------------------------------------------------
    // FORGOT / RESET PASSWORD (OTP-based)
    // -------------------------------------------------------------------------

    /**
     * Step 1 — send OTP to email for password reset.
     *
     * POST /api/auth/forgot-password
     * Body: email
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Always return the same response regardless of whether email exists
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If your email exists, you will receive a password reset OTP shortly.',
                'data'    => ['needs_otp_verification' => true],
            ], 200);
        }

        ['model' => $otpRecord, 'plain_otp' => $plainOtp] = OtpVerification::generate(
            $request->email, 'forgot_password'
        );

        $sent = $this->sendOtpEmail($request->email, $plainOtp, 'forgot_password', $user->name);

        return response()->json([
            'success' => true,
            'message' => 'If your email exists, you will receive a password reset OTP shortly.',
            'data'    => [
                'needs_otp_verification' => true,
                'email'                  => $request->email,
                'otp_expires_at'         => $otpRecord->expires_at->toIso8601String(),
                'email_sent'             => $sent,
            ],
        ], 200);
    }

    /**
     * Step 2 — verify OTP and set new password.
     *
     * POST /api/auth/reset-password
     * Body: email, otp, password, password_confirmation
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'otp'      => 'required|string|size:6',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $otpRecord = OtpVerification::findValid($request->email, 'forgot_password');

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired or is invalid. Please request a new one.',
                'errors'  => ['otp' => ['This OTP has expired. Please request a new one.']],
            ], 422);
        }

        if (!$otpRecord->verify($request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please check and try again.',
                'errors'  => ['otp' => ['The OTP you entered is incorrect.']],
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Delete OTP record and revoke all tokens (force re-login everywhere)
        $otpRecord->delete();
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. Please login with your new password.',
        ], 200);
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    /**
     * Configure dynamic SMTP and dispatch the OTP email.
     * Returns true on success, false on failure.
     */
    private function sendOtpEmail(string $email, string $otp, string $type, string $name = 'there'): bool
    {
        try {
            MailService::configure();
            Mail::to($email)->send(new OtpMail($otp, $type, $name));
            return true;
        } catch (\Exception $e) {
            \Log::error('OTP email failed: ' . $e->getMessage(), ['email' => $email, 'type' => $type]);
            return false;
        }
    }
}
