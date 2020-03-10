<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\HomeController;
use App\Models\User;
/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Userlogin"},
 *     summary="User Login",
 *     description="Logs in a user and returns user details and redirect URL",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Login successful"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="image", type="string", format="url", example="http://yourdomain.com/storage/properties/room.jpg"),
 *                 @OA\Property(property="email", type="string")
 *             ),
 *             @OA\Property(property="redirect_url", type="string", example="http://your-app-url/home"),
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOi...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
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
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        // Check if email is verified
        if (is_null($user->email_verified_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Please verify your email before logging in. Check your email for the verification code.',
                'requires_verification' => true
            ], 403);
        }
    
        // Generate token
        $token = $user->createToken('api-token')->plainTextToken;
    
        // Optional: Log in user for session-based access (useful if not using token for everything)
        Auth::login($user);
    
        // Create the response data
        $data = [
            'message' => 'Login successful',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'image' => asset('storage/' . $user->image),
                'role'  => $user->role, 
            ],

            'token' => $token,
        ];
    
        return response()->json($data);
    }
    
    
}
