<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="General Housing Admin",
 *     description="Admin endpoints for general housing management"
 * )
 */
class GeneralAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/admin/general/properties",
     *     tags={"General Housing Admin"},
     *     summary="Get all general housing properties (admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of all properties"
     *     ),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function getProperties(Request $request)
    {
        $query = Properties::where('housing_context', 'general')
            ->with('user:id,name,email,phone');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        $properties = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $properties
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/general/users",
     *     tags={"General Housing Admin"},
     *     summary="Get all general housing users (admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter by role (tenant, landlord)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users"
     *     )
     * )
     */
    public function getUsers(Request $request)
    {
        $query = User::where('housing_context', 'general');

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'profile_complete' => $user->profile_complete,
                    'created_at' => $user->created_at,
                ];
            }),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/admin/general/property/{id}/status",
     *     tags={"General Housing Admin"},
     *     summary="Update property status (approve/suspend)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"Available", "Suspended", "Pending"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated"),
     *     @OA\Response(response=404, description="Property not found")
     * )
     */
    public function updatePropertyStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:Available,Suspended,Pending',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $property = Properties::where('id', $id)
            ->where('housing_context', 'general')
            ->first();

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $property->update(['status' => $request->status]);

        return response()->json([
            'status' => true,
            'message' => 'Property status updated successfully',
            'data' => [
                'id' => $property->id,
                'title' => $property->title,
                'status' => $property->status,
            ]
        ], 200);
    }
}
