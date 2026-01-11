<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\HomeController;
use App\Models\User;
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
        'email'    => 'required_without:phone|email',
        'phone'    => 'required_without:email|string',
        'password' => 'required|string|min:6',
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

    // Optional: revoke old tokens
    $user->tokens()->delete();

    // Generate Sanctum token
    $token = $user->createToken('api-token')->plainTextToken;

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
    ]);
}



}
