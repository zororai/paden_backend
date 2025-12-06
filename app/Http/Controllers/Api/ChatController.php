<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Chat",
 *     description="Real-time chat endpoints using Pusher"
 * )
 */
class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Post(
     *     path="/api/chat/send",
     *     tags={"Chat"},
     *     summary="Send a message",
     *     description="Send a message to another user via Pusher",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"receiver_id", "message"},
     *             @OA\Property(property="receiver_id", type="integer", example=2),
     *             @OA\Property(property="message", type="string", example="Hello, how are you?")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Message sent successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(property="created_at", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Receiver not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $sender = auth()->user();
        $receiverId = $request->receiver_id;

        // Check if receiver exists
        $receiver = User::find($receiverId);
        if (!$receiver) {
            return response()->json([
                'status' => false,
                'message' => 'Receiver not found.'
            ], 404);
        }

        // Prevent sending message to self
        if ($sender->id == $receiverId) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot send a message to yourself.'
            ], 400);
        }

        // Create message
        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        // Broadcast the message via Pusher
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'created_at' => $message->created_at->toISOString(),
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/chat/messages/{userId}",
     *     tags={"Chat"},
     *     summary="Get chat messages",
     *     description="Get all messages between authenticated user and another user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID of the other user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Messages retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="message", type="string"),
     *                     @OA\Property(property="sender_id", type="integer"),
     *                     @OA\Property(property="receiver_id", type="integer"),
     *                     @OA\Property(property="is_read", type="boolean"),
     *                     @OA\Property(property="created_at", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getMessages($userId)
    {
        $authUser = auth()->user();

        // Check if the other user exists
        $otherUser = User::find($userId);
        if (!$otherUser) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Get all messages between the two users
        $messages = Message::betweenUsers($authUser->id, $userId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at->toISOString(),
                ];
            });

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $authUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'data' => $messages
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/chat/conversations",
     *     tags={"Chat"},
     *     summary="Get all conversations",
     *     description="Get list of all users the authenticated user has chatted with",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Conversations retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="user_name", type="string"),
     *                     @OA\Property(property="user_image", type="string"),
     *                     @OA\Property(property="last_message", type="string"),
     *                     @OA\Property(property="last_message_time", type="string"),
     *                     @OA\Property(property="unread_count", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getConversations()
    {
        $authUser = auth()->user();

        // Get all users the authenticated user has exchanged messages with
        $conversations = Message::where('sender_id', $authUser->id)
            ->orWhere('receiver_id', $authUser->id)
            ->with(['sender', 'receiver'])
            ->get()
            ->groupBy(function ($message) use ($authUser) {
                return $message->sender_id == $authUser->id
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function ($messages, $userId) use ($authUser) {
                $lastMessage = $messages->sortByDesc('created_at')->first();
                $otherUser = $lastMessage->sender_id == $authUser->id
                    ? $lastMessage->receiver
                    : $lastMessage->sender;

                $unreadCount = Message::where('sender_id', $userId)
                    ->where('receiver_id', $authUser->id)
                    ->where('is_read', false)
                    ->count();

                return [
                    'user_id' => $otherUser->id,
                    'user_name' => $otherUser->name,
                    'user_image' => asset('storage/' . $otherUser->image),
                    'last_message' => $lastMessage->message,
                    'last_message_time' => $lastMessage->created_at->toISOString(),
                    'unread_count' => $unreadCount,
                ];
            })
            ->values();

        return response()->json([
            'status' => true,
            'data' => $conversations
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/chat/mark-read/{userId}",
     *     tags={"Chat"},
     *     summary="Mark messages as read",
     *     description="Mark all messages from a specific user as read",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID of the sender",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Messages marked as read",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Messages marked as read")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function markAsRead($userId)
    {
        $authUser = auth()->user();

        Message::where('sender_id', $userId)
            ->where('receiver_id', $authUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Messages marked as read'
        ], 200);
    }
}
