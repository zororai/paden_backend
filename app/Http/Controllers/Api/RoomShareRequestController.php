<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomShareRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Room Share Requests",
 *     description="Operations related to room share requests between students"
 * )
 */
class RoomShareRequestController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/room-share/send",
     *     tags={"Room Share Requests"},
     *     summary="Send a room share request to another student",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"receiver_id", "property_id"},
     *             @OA\Property(property="receiver_id", type="integer", example=2),
     *             @OA\Property(property="property_id", type="integer", example=5),
     *             @OA\Property(property="message", type="string", example="Hi, I'm looking for a roommate. Would you like to share a room?")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Request sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room share request sent successfully."),
     *             @OA\Property(property="request", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Request already exists"
     *     )
     * )
     */
    public function sendRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer|exists:users,id',
            'property_id' => 'required|integer|exists:properties,id',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $senderId = Auth::id();
        $receiverId = $request->input('receiver_id');
        $propertyId = $request->input('property_id');

        if ($senderId == $receiverId) {
            return response()->json(['message' => 'You cannot send a request to yourself.'], 400);
        }

        $existingRequest = RoomShareRequest::where(function($query) use ($senderId, $receiverId, $propertyId) {
            $query->where('sender_id', $senderId)
                  ->where('receiver_id', $receiverId)
                  ->where('property_id', $propertyId);
        })->orWhere(function($query) use ($senderId, $receiverId, $propertyId) {
            $query->where('sender_id', $receiverId)
                  ->where('receiver_id', $senderId)
                  ->where('property_id', $propertyId);
        })->first();

        if ($existingRequest) {
            return response()->json(['message' => 'A request already exists between you and this user for this property.'], 409);
        }

        $roomShareRequest = RoomShareRequest::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'property_id' => $propertyId,
            'message' => $request->input('message'),
            'status' => 'pending',
        ]);

        $roomShareRequest->load(['sender', 'receiver', 'property']);

        return response()->json([
            'message' => 'Room share request sent successfully.',
            'request' => $roomShareRequest
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/room-share/sent",
     *     tags={"Room Share Requests"},
     *     summary="Get all room share requests sent by the authenticated user",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved sent requests",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sent requests retrieved successfully."),
     *             @OA\Property(property="requests", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getSentRequests()
    {
        $userId = Auth::id();

        $requests = RoomShareRequest::where('sender_id', $userId)
            ->with([
                'receiver' => function($query) {
                    $query->select('id', 'name', 'surname', 'email', 'university', 'image', 'phone');
                },
                'property'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $transformed = $requests->map(function ($request) {
            return [
                'id' => $request->id,
                'receiver' => [
                    'id' => $request->receiver->id,
                    'name' => $request->receiver->name,
                    'surname' => $request->receiver->surname,
                    'email' => $request->receiver->email,
                    'university' => $request->receiver->university,
                    'phone' => $request->receiver->phone,
                    'image' => $request->receiver->image ? asset('storage/' . $request->receiver->image) : null,
                ],
                'property' => [
                    'id' => $request->property->id,
                    'title' => $request->property->title,
                    'price' => $request->property->price,
                    'location' => $request->property->location,
                    'image' => $request->property->pimage ? asset('storage/' . $request->property->pimage) : null,
                ],
                'message' => $request->message,
                'status' => $request->status,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Sent requests retrieved successfully.',
            'requests' => $transformed
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/room-share/received",
     *     tags={"Room Share Requests"},
     *     summary="Get all room share requests received by the authenticated user",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved received requests",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Received requests retrieved successfully."),
     *             @OA\Property(property="requests", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getReceivedRequests()
    {
        $userId = Auth::id();

        $requests = RoomShareRequest::where('receiver_id', $userId)
            ->with([
                'sender' => function($query) {
                    $query->select('id', 'name', 'surname', 'email', 'university', 'image', 'phone');
                },
                'property'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $transformed = $requests->map(function ($request) {
            return [
                'id' => $request->id,
                'sender' => [
                    'id' => $request->sender->id,
                    'name' => $request->sender->name,
                    'surname' => $request->sender->surname,
                    'email' => $request->sender->email,
                    'university' => $request->sender->university,
                    'phone' => $request->sender->phone,
                    'image' => $request->sender->image ? asset('storage/' . $request->sender->image) : null,
                ],
                'property' => [
                    'id' => $request->property->id,
                    'title' => $request->property->title,
                    'price' => $request->property->price,
                    'location' => $request->property->location,
                    'image' => $request->property->pimage ? asset('storage/' . $request->property->pimage) : null,
                ],
                'message' => $request->message,
                'status' => $request->status,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Received requests retrieved successfully.',
            'requests' => $transformed
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/room-share/accept/{id}",
     *     tags={"Room Share Requests"},
     *     summary="Accept a room share request",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request accepted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room share request accepted."),
     *             @OA\Property(property="request", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Request not found"
     *     )
     * )
     */
    public function acceptRequest($id)
    {
        $userId = Auth::id();

        $request = RoomShareRequest::where('id', $id)
            ->where('receiver_id', $userId)
            ->first();

        if (!$request) {
            return response()->json(['message' => 'Request not found or unauthorized.'], 404);
        }

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'This request has already been processed.'], 400);
        }

        $request->status = 'accepted';
        $request->save();

        $request->load(['sender', 'receiver', 'property']);

        return response()->json([
            'message' => 'Room share request accepted.',
            'request' => $request
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/room-share/reject/{id}",
     *     tags={"Room Share Requests"},
     *     summary="Reject a room share request",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request rejected successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room share request rejected."),
     *             @OA\Property(property="request", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Request not found"
     *     )
     * )
     */
    public function rejectRequest($id)
    {
        $userId = Auth::id();

        $request = RoomShareRequest::where('id', $id)
            ->where('receiver_id', $userId)
            ->first();

        if (!$request) {
            return response()->json(['message' => 'Request not found or unauthorized.'], 404);
        }

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'This request has already been processed.'], 400);
        }

        $request->status = 'rejected';
        $request->save();

        $request->load(['sender', 'receiver', 'property']);

        return response()->json([
            'message' => 'Room share request rejected.',
            'request' => $request
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/room-share/{id}",
     *     tags={"Room Share Requests"},
     *     summary="Delete a room share request",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room share request deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Request not found"
     *     )
     * )
     */
    public function deleteRequest($id)
    {
        $userId = Auth::id();

        $request = RoomShareRequest::where('id', $id)
            ->where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->first();

        if (!$request) {
            return response()->json(['message' => 'Request not found or unauthorized.'], 404);
        }

        $request->delete();

        return response()->json(['message' => 'Room share request deleted successfully.'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/room-share/student/{id}",
     *     tags={"Room Share Requests"},
     *     summary="View a student's profile",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student profile retrieved successfully."),
     *             @OA\Property(property="student", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */
    public function getStudentProfile($id)
    {
        $student = User::select('id', 'name', 'surname', 'email', 'university', 'type', 'image', 'phone')
            ->find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $profile = [
            'id' => $student->id,
            'name' => $student->name,
            'surname' => $student->surname,
            'email' => $student->email,
            'university' => $student->university,
            'type' => $student->type,
            'phone' => $student->phone,
            'image' => $student->image ? asset('storage/' . $student->image) : null,
        ];

        return response()->json([
            'message' => 'Student profile retrieved successfully.',
            'student' => $profile
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/room-share/students",
     *     tags={"Room Share Requests"},
     *     summary="Get list of students at the same university",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="university",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Students retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Students retrieved successfully."),
     *             @OA\Property(property="students", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getStudents(Request $request)
    {
        $userId = Auth::id();
        $currentUser = Auth::user();
        
        $university = $request->query('university', $currentUser->university);

        $students = User::select('id', 'name', 'surname', 'email', 'university', 'type', 'image', 'phone')
            ->where('id', '!=', $userId)
            ->where('university', $university)
            ->get();

        $transformed = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'surname' => $student->surname,
                'email' => $student->email,
                'university' => $student->university,
                'type' => $student->type,
                'phone' => $student->phone,
                'image' => $student->image ? asset('storage/' . $student->image) : null,
            ];
        });

        return response()->json([
            'message' => 'Students retrieved successfully.',
            'students' => $transformed
        ], 200);
    }
}
