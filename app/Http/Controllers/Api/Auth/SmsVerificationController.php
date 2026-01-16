<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use App\Models\UserDevice;
use App\Helpers\SmsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="SMS Verification",
 *     description="SMS-based authentication endpoints"
 * )
 */
class SmsVerificationController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/login/request-code",
     *     tags={"SMS Verification"},
     *     summary="Request SMS verification code for login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "password"},
     *             @OA\Property(property="phone", type="string", example="+263771234567"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification code sent"
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function requestCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => 'required|string',
            'password' => 'required|string|min:6',
            'device_id'   => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'platform'    => 'nullable|in:android,ios,web',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Format phone number to international format
        $formattedPhone = SmsHelper::formatPhoneNumber($request->phone);

        // Try to find user with formatted phone first, then raw phone (for backward compatibility)
        $user = User::where('phone', $formattedPhone)->first();
        if (!$user) {
            $user = User::where('phone', $request->phone)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Check if phone is already verified
        if ($user->phone_verified_at !== null) {
            // Phone already verified, skip SMS and log in directly
            $deviceId = $request->device_id ?? 'default-device';
            $tokenName = "device:{$deviceId}";

            $user->tokens()->where('name', $tokenName)->delete();

            if ($request->device_id) {
                $device = UserDevice::updateOrCreate(
                    [
                        'user_id'   => $user->id,
                        'device_id' => $deviceId,
                    ],
                    [
                        'device_name'  => $request->device_name,
                        'platform'     => $request->platform,
                        'last_seen_at' => now(),
                        'remember_me'  => true,
                    ]
                );
            }

            $token = $user->createToken($tokenName)->plainTextToken;

            return response()->json([
                'status'  => true,
                'already_verified' => true,
                'message' => 'Phone already verified, login successful',
                'user'    => [
                    'id'               => $user->id,
                    'name'             => $user->name,
                    'phone'            => $user->phone,
                    'email'            => $user->email,
                    'role'             => $user->role,
                    'housing_context'  => $user->housing_context ?? 'student',
                    'profile_complete' => $user->profile_complete ?? false,
                    'image'            => $user->image ? asset('storage/' . $user->image) : null,
                ],
                'token' => $token,
                'device' => $request->device_id ? [
                    'device_id'   => $device->device_id,
                    'platform'    => $device->platform,
                    'remember_me' => $device->remember_me,
                ] : null,
            ], 200);
        }

        // Store verification code with formatted phone for consistency
        $verificationCode = VerificationCode::createForPhone($formattedPhone);

        $smsResult = SmsHelper::sendVerificationCode(
            $formattedPhone,
            $verificationCode->code
        );

        if (!$smsResult['success']) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to send verification code. Please try again.',
                'error' => $smsResult['message'] ?? 'Unknown error',
            ], 500);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Verification code sent to your phone',
            'expires_in' => 120,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/login/verify-code",
     *     tags={"SMS Verification"},
     *     summary="Verify SMS code and complete login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "code", "device_id", "platform"},
     *             @OA\Property(property="phone", type="string", example="+263771234567"),
     *             @OA\Property(property="code", type="string", example="123456"),
     *             @OA\Property(property="device_id", type="string", example="device-unique-id"),
     *             @OA\Property(property="device_name", type="string", example="iPhone 13"),
     *             @OA\Property(property="platform", type="string", enum={"android", "ios", "web"}, example="android")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful"
     *     ),
     *     @OA\Response(response=401, description="Invalid or expired code")
     * )
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'       => 'required|string',
            'code'        => 'required|string|size:6',
            'device_id'   => 'required|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'platform'    => 'required|in:android,ios,web',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Format phone number to match stored format
        $formattedPhone = SmsHelper::formatPhoneNumber($request->phone);

        $verificationCode = VerificationCode::where('phone', $formattedPhone)
            ->where('code', $request->code)
            ->where('verified', false)
            ->first();

        if (!$verificationCode) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid verification code',
            ], 401);
        }

        if ($verificationCode->isExpired()) {
            return response()->json([
                'status'  => false,
                'message' => 'Verification code has expired',
            ], 401);
        }

        $verificationCode->update(['verified' => true]);

        // Try to find user with formatted phone first, then raw phone
        $user = User::where('phone', $formattedPhone)->first();
        if (!$user) {
            $user = User::where('phone', $request->phone)->first();
        }

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        // Mark phone as verified
        if ($user->phone_verified_at === null) {
            $user->phone_verified_at = now();
            $user->save();
        }

        $deviceId = $request->device_id;
        $tokenName = "device:{$deviceId}";

        $user->tokens()->where('name', $tokenName)->delete();

        $device = UserDevice::updateOrCreate(
            [
                'user_id'   => $user->id,
                'device_id' => $deviceId,
            ],
            [
                'device_name'  => $request->device_name,
                'platform'     => $request->platform,
                'last_seen_at' => now(),
                'remember_me'  => true,
            ]
        );

        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'user'    => [
                'id'               => $user->id,
                'name'             => $user->name,
                'phone'            => $user->phone,
                'email'            => $user->email,
                'role'             => $user->role,
                'housing_context'  => $user->housing_context ?? 'student',
                'profile_complete' => $user->profile_complete ?? false,
                'image'            => $user->image ? asset('storage/' . $user->image) : null,
            ],
            'token' => $token,
            'device' => [
                'device_id'   => $device->device_id,
                'platform'    => $device->platform,
                'remember_me' => $device->remember_me,
            ],
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/login/resend-code",
     *     tags={"SMS Verification"},
     *     summary="Resend verification code",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="+263771234567")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification code resent"
     *     )
     * )
     */
    public function resendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Format phone number to international format
        $formattedPhone = SmsHelper::formatPhoneNumber($request->phone);

        // Try to find user with formatted phone first, then raw phone
        $user = User::where('phone', $formattedPhone)->first();
        if (!$user) {
            $user = User::where('phone', $request->phone)->first();
        }

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Phone number not found',
            ], 404);
        }

        // Store verification code with formatted phone for consistency
        $verificationCode = VerificationCode::createForPhone($formattedPhone);

        $smsResult = SmsHelper::sendVerificationCode(
            $formattedPhone,
            $verificationCode->code
        );

        if (!$smsResult['success']) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to send verification code. Please try again.',
                'error' => $smsResult['message'] ?? 'Unknown error',
            ], 500);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Verification code resent to your phone',
            'expires_in' => 120,
        ], 200);
    }
}
