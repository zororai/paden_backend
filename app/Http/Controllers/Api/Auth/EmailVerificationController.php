<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Email_Verification",
 *     description="Email verification endpoints"
 * )
 */
class EmailVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Post(
     *     path="/api/email/verify",
     *     tags={"Email_Verification"},
     *     summary="Verify email with code",
     *     description="Verify user's email using the 6-digit verification code",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email verified successfully.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid or expired code"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Email is already verified.'
            ], 400);
        }

        if (EmailVerificationCode::verify($user->email, $request->code)) {
            $user->markEmailAsVerified();

            return response()->json([
                'status' => true,
                'message' => 'Email verified successfully.'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid or expired verification code.'
        ], 400);
    }

    /**
     * @OA\Post(
     *     path="/api/email/resend",
     *     tags={"Email_Verification"},
     *     summary="Resend verification code",
     *     description="Resend a new code to the user's email",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Code sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Verification code sent to your email.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Email already verified"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function resend(Request $request)
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Email is already verified.'
            ], 400);
        }

        $verificationCode = EmailVerificationCode::createForEmail($user->email);

        $user->notify(new EmailVerificationNotification($verificationCode->code));

        return response()->json([
            'status' => true,
            'message' => 'Verification code sent to your email.'
        ], 200);
    }
}
