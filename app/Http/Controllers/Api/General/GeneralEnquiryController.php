<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Properties;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="General Housing Enquiries",
 *     description="Tenant enquiry endpoints for contacting landlords"
 * )
 */
class GeneralEnquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Post(
     *     path="/api/general/enquiries",
     *     tags={"General Housing Enquiries"},
     *     summary="Send an enquiry to a landlord about a property",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id", "message"},
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Hi, I'm interested in this property. Is it still available?")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Enquiry sent successfully"
     *     ),
     *     @OA\Response(response=404, description="Property not found"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|integer|exists:properties,id',
            'message'     => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $property = Properties::where('id', $request->property_id)
            ->where('housing_context', 'general')
            ->first();

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $sender = auth()->user();
        $receiverId = $property->uid;

        if ($sender->id == $receiverId) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot send an enquiry to yourself.'
            ], 400);
        }

        $enquiryMessage = "[Property Enquiry: {$property->title}]\n\n{$request->message}";

        $message = Message::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiverId,
            'message'     => $enquiryMessage,
        ]);

        if (class_exists('App\Events\MessageSent')) {
            broadcast(new MessageSent($message))->toOthers();
        }

        return response()->json([
            'status' => true,
            'message' => 'Enquiry sent successfully',
            'data' => [
                'id' => $message->id,
                'property_id' => $property->id,
                'property_title' => $property->title,
                'landlord_id' => $receiverId,
                'created_at' => $message->created_at->toISOString(),
            ]
        ], 201);
    }
}
