<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\HomeController;
use App\Models\User;
use App\Models\UserDevice;
/**
 /**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Userlogin"},
 *     summary="User Login (Email or Phone)",
 *     description="Login using either email OR phone with password",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     required={"email","password"},
 *                     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *                     @OA\Property(property="password", type="string", format="password", example="password123")
 *                 ),
 *                 @OA\Schema(
 *                     required={"phone","password"},
 *                     @OA\Property(property="phone", type="string", example="+263771234567"),
 *                     @OA\Property(property="password", type="string", format="password", example="password123")
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Login successful"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", nullable=true, example="john@example.com"),
 *                 @OA\Property(property="phone", type="string", nullable=true, example="+263771234567"),
 *                 @OA\Property(property="role", type="string", example="teacher"),
 *                 @OA\Property(property="image", type="string", nullable=true, example="https://yourdomain.com/storage/avatar.jpg")
 *             ),
 *             @OA\Property(property="token", type="string", example="1|XyZabc123...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid credentials")
 *         )
 *     )
 * )
 */


class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required_without:phone|email',
            'phone'     => 'required_without:email|string',
            'password'  => 'required|string|min:6',
            'device_id' => 'required|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'platform'  => 'required|in:android,ios,web',
        ]);

        // Find user by email or phone
        $user = $request->email
            ? User::where('email', $request->email)->first()
            : User::where('phone', $request->phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $deviceId = $request->device_id;
        $tokenName = "device:{$deviceId}";

        // Revoke existing token for this device only (one token per device)
        $user->tokens()->where('name', $tokenName)->delete();

        // Update or create device record
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

        // Generate Sanctum token with device-specific name
        $token = $user->createToken($tokenName)->plainTextToken;

        // Return ONLY the identifier used to log in
        $loginIdentifier = $request->email
            ? ['email' => $user->email]
            : ['phone' => $user->phone];

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'user'    => array_merge([
                'id'              => $user->id,
                'name'            => $user->name,
                'role'            => $user->role,
                'housing_context' => $user->housing_context ?? 'student',
                'profile_complete'=> $user->profile_complete ?? false,
                'image'           => $user->image
                    ? asset('storage/' . $user->image)
                    : null,
            ], $loginIdentifier),
            'token' => $token,
            'device' => [
                'device_id'   => $device->device_id,
                'platform'    => $device->platform,
                'remember_me' => $device->remember_me,
            ],
        ]);
    }

    /**
     * Get authenticated user info (auto-login check).
     * Updates last_seen_at for the device.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();

        // Extract device_id from token name (format: "device:{device_id}")
        $tokenName = $token->name;
        $deviceId = str_replace('device:', '', $tokenName);

        // Update last_seen_at for this device
        $device = UserDevice::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();

        if ($device) {
            $device->touchLastSeen();
        }

        return response()->json([
            'status' => true,
            'user'   => [
                'id'              => $user->id,
                'name'            => $user->name,
                'surname'         => $user->surname,
                'email'           => $user->email,
                'phone'           => $user->phone,
                'role'            => $user->role,
                'housing_context' => $user->housing_context ?? 'student',
                'profile_complete'=> $user->profile_complete ?? false,
                'image'           => $user->image
                    ? asset('storage/' . $user->image)
                    : null,
            ],
            'device' => $device ? [
                'device_id'    => $device->device_id,
                'device_name'  => $device->device_name,
                'platform'     => $device->platform,
                'last_seen_at' => $device->last_seen_at,
            ] : null,
        ]);
    }

    /**
     * Logout from current device only.
     * Deletes the current access token.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();

        // Extract device_id from token name
        $tokenName = $token->name;
        $deviceId = str_replace('device:', '', $tokenName);

        // Delete the device record
        UserDevice::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->delete();

        // Delete current token
        $token->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Logout from all devices.
     * Deletes ALL tokens and device records for the user.
     */
    public function logoutAll(Request $request)
    {
        $user = $request->user();

        // Delete all device records for this user
        UserDevice::where('user_id', $user->id)->delete();

        // Delete all tokens for this user
        $user->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out from all devices successfully',
        ]);
    }

    /**
     * Get list of all devices for the authenticated user.
     */
    public function devices(Request $request)
    {
        $user = $request->user();
        $currentToken = $user->currentAccessToken();
        $currentDeviceId = str_replace('device:', '', $currentToken->name);

        $devices = UserDevice::where('user_id', $user->id)
            ->orderBy('last_seen_at', 'desc')
            ->get()
            ->map(function ($device) use ($currentDeviceId) {
                return [
                    'device_id'    => $device->device_id,
                    'device_name'  => $device->device_name,
                    'platform'     => $device->platform,
                    'last_seen_at' => $device->last_seen_at,
                    'is_current'   => $device->device_id === $currentDeviceId,
                ];
            });

        return response()->json([
            'status'  => true,
            'devices' => $devices,
        ]);
    }

    /**
     * Logout from a specific device by device_id.
     */
    public function logoutDevice(Request $request, string $deviceId)
    {
        $user = $request->user();
        $tokenName = "device:{$deviceId}";

        // Delete the device record
        $deleted = UserDevice::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'status'  => false,
                'message' => 'Device not found',
            ], 404);
        }

        // Delete the token for this device
        $user->tokens()->where('name', $tokenName)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Device logged out successfully',
        ]);
    }



}
