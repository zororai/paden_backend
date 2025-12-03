<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerificationCode;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="User_Reg",
 *     description="User registration and management"
 */

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John"),
 *     @OA\Property(property="surname", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", example="john@example.com"),
 *     @OA\Property(property="university", type="string", example="Rose of Sharon University"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/useregister",
     *     tags={"User_Reg"},
     *     summary="Register a new user",
     *     description="Handles the registration of a new user by accepting required fields and returning the user information upon success.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "surname", "email", "password", "password_confirmation", "university", "type", "phone", "role"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="surname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="university", type="string", example="Rose of Sharon University"),
     *             @OA\Property(property="role", type="string", example="user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully."),
     *             @OA\Property(property="token", type="string", example="generated_api_token"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'surname'    => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'university' => 'required|string|max:255',
            'password'   => 'required|string|min:6|confirmed',
            'role'       => 'required|string|max:255',
        ],[
            'email.unique' => 'This email address is already registered.',
            'password.confirmed' => 'Passwords do not match.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $image = "new";
        $user = User::create([
            'name'       => $request->name,
            'surname'    => $request->surname,
            'email'      => $request->email,
            'university' => $request->university,
            'type'       => $image,
            'image'      => $image,
            'phone'      => $image,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully.',
            'token' => $token,
            'user' => $user
        ], 201);
    }
}
