<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="General Housing Registration",
 *     description="Registration endpoints for general housing users"
 * )
 */
class GeneralRegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register/general",
     *     tags={"General Housing Registration"},
     *     summary="Register a new general housing user",
     *     description="Registers a new tenant or landlord for general housing",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "password", "password_confirmation", "role"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="surname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="+263771234567"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"tenant", "landlord"}, example="tenant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'surname'  => 'nullable|string|max:255',
            'email'    => 'nullable|string|email|max:255|unique:users',
            'phone'    => 'required_without:email|string|max:20|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|string|in:tenant,landlord',
        ], [
            'email.unique' => 'This email address is already registered.',
            'phone.unique' => 'This phone number is already registered.',
            'password.confirmed' => 'Passwords do not match.',
            'role.in' => 'Role must be either tenant or landlord.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'            => $request->name,
            'surname'         => $request->surname,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'password'        => Hash::make($request->password),
            'role'            => $request->role,
            'housing_context' => 'general',
            'profile_complete' => false,
            'image'           => 'new',
            'type'            => 'general',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'housing_context' => $user->housing_context,
                'profile_complete' => $user->profile_complete,
            ]
        ], 201);
    }
}
