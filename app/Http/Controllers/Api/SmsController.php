<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="SMS",
 *     description="SMS notification endpoints"
 * )
 */
class SmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Post(
     *     path="/api/sms/send/{userId}",
     *     tags={"SMS"},
     *     summary="Send SMS to a user",
     *     description="Send house inquiry SMS to a user's phone number",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="User ID (property owner)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"propertyAddress"},
     *             @OA\Property(property="propertyAddress", type="string", example="123 Main Street, Harare")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SMS sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="SMS sent successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="destination", type="string"),
     *                 @OA\Property(property="messageText", type="string"),
     *                 @OA\Property(property="response", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=400, description="User has no phone number"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="SMS API error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function sendSms(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'propertyAddress' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get authenticated user (sender)
        $authUser = auth()->user();

        // Find the user (property owner)
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check if user has a phone number
        if (!$user->phone) {
            return response()->json([
                'status' => false,
                'message' => 'User has no phone number'
            ], 400);
        }

        // Prepare SMS API credentials
        $username = env('INBOXIQ_USERNAME');
        $password = env('INBOXIQ_PASSWORD');
        $apiKey = env('INBOXIQ_API_KEY');

        if (!$username || !$password || !$apiKey) {
            return response()->json([
                'status' => false,
                'message' => 'SMS API credentials not configured'
            ], 500);
        }

        // Create predefined message
        $messageText = "{$authUser->name} is looking for a student accommodation. Is the house at {$request->propertyAddress} still available?";

        // Create Basic Auth header
        $basicAuth = base64_encode($username . ':' . $password);

        try {
            // Send SMS via InboxIQ API
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $basicAuth,
                'key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.inboxiq.co.zw/api/v1/send-sms', [
                'destination' => $user->phone,
                'messageText' => $messageText,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => true,
                    'message' => 'SMS sent successfully',
                    'data' => [
                        'destination' => $user->phone,
                        'messageText' => $messageText,
                        'response' => $response->json()
                    ]
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send SMS',
                    'error' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'SMS API error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
