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
     *     summary="Post a room share request to all students at your university",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id"},
     *             @OA\Property(property="property_id", type="integer", example=5),
     *             @OA\Property(property="message", type="string", example="Hi, I'm looking for a roommate. Would you like to share a room?"),
     *             @OA\Property(property="preferred_year", type="string", example="2nd Year"),
     *             @OA\Property(property="preferred_gender", type="string", example="Male"),
     *             @OA\Property(property="rent_sharing_conditions", type="string", example="50/50 split on rent and utilities")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Request posted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room share request posted successfully to all students at your university."),
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
            'property_id' => 'required|integer|exists:properties,id',
            'message' => 'nullable|string|max:1000',
            'preferred_year' => 'nullable|string|max:50',
            'preferred_gender' => 'nullable|string|max:20',
            'rent_sharing_conditions' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $senderId = Auth::id();
        $sender = Auth::user();
        $propertyId = $request->input('property_id');

        $existingRequest = RoomShareRequest::where('sender_id', $senderId)
            ->where('property_id', $propertyId)
            ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'You have already posted a room share request for this property.'], 409);
        }

        $roomShareRequest = RoomShareRequest::create([
            'sender_id' => $senderId,
            'property_id' => $propertyId,
            'university' => $sender->university,
            'message' => $request->input('message'),
            'preferred_year' => $request->input('preferred_year'),
            'preferred_gender' => $request->input('preferred_gender'),
            'rent_sharing_conditions' => $request->input('rent_sharing_conditions'),
            'status' => 'pending',
        ]);

        $roomShareRequest->load(['sender', 'property']);

        return response()->json([
            'message' => 'Room share request posted successfully to all students at your university.',
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
            ->with(['sender', 'property'])
            ->orderBy('created_at', 'desc')
            ->get();

        $transformed = $requests->map(function ($request) {
            return [
                'id' => $request->id,
                'university' => $request->university,
                'property' => [
                    'id' => $request->property->id,
                    'title' => $request->property->title,
                    'price' => $request->property->price,
                    'location' => $request->property->location,
                    'image' => $request->property->pimage ? asset('storage/' . $request->property->pimage) : null,
                ],
                'message' => $request->message,
                'preferred_year' => $request->preferred_year,
                'preferred_gender' => $request->preferred_gender,
                'rent_sharing_conditions' => $request->rent_sharing_conditions,
                'status' => $request->status,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Your room share posts retrieved successfully.',
            'requests' => $transformed
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/room-share/university",
     *     tags={"Room Share Requests"},
     *     summary="Get all room share posts from students at your university",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved university room share posts",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="University room share posts retrieved successfully."),
     *             @OA\Property(property="requests", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getUniversityPosts()
    {
        $userId = Auth::id();
        $user = Auth::user();

        $requests = RoomShareRequest::where('university', $user->university)
            ->where('sender_id', '!=', $userId)
            ->where('status', 'pending')
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
                'preferred_year' => $request->preferred_year,
                'preferred_gender' => $request->preferred_gender,
                'rent_sharing_conditions' => $request->rent_sharing_conditions,
                'status' => $request->status,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ];
        });

        return response()->json([
            'message' => 'University room share posts retrieved successfully.',
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
