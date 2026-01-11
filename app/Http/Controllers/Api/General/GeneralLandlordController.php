<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="General Housing Landlord",
 *     description="Landlord profile and property management endpoints"
 * )
 */
class GeneralLandlordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/api/general/landlord/profile/status",
     *     tags={"General Housing Landlord"},
     *     summary="Get landlord profile completion status",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile status"
     *     )
     * )
     */
    public function profileStatus()
    {
        $user = auth()->user();

        return response()->json([
            'status' => true,
            'profile_complete' => $user->profile_complete,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'preferred_contact' => $user->preferred_contact,
                'whatsapp_enabled' => $user->whatsapp_enabled,
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/general/landlord/profile",
     *     tags={"General Housing Landlord"},
     *     summary="Complete or update landlord profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"full_name", "phone", "preferred_contact"},
     *             @OA\Property(property="full_name", type="string", example="John Doe"),
     *             @OA\Property(property="phone", type="string", example="+263771234567"),
     *             @OA\Property(property="preferred_contact", type="string", example="phone"),
     *             @OA\Property(property="whatsapp_enabled", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully"
     *     ),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name'         => 'required|string|max:255',
            'phone'             => 'required|string|max:20',
            'preferred_contact' => 'required|string|in:phone,email,whatsapp',
            'whatsapp_enabled'  => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $user->update([
            'name'              => $request->full_name,
            'phone'             => $request->phone,
            'preferred_contact' => $request->preferred_contact,
            'whatsapp_enabled'  => $request->whatsapp_enabled ?? false,
            'profile_complete'  => true,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'profile_complete' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'preferred_contact' => $user->preferred_contact,
                'whatsapp_enabled' => $user->whatsapp_enabled,
            ]
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/general/landlord/properties",
     *     tags={"General Housing Landlord"},
     *     summary="Get landlord's own properties",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of landlord's properties"
     *     )
     * )
     */
    public function getProperties()
    {
        $user = auth()->user();
        
        $properties = Properties::where('uid', $user->id)
            ->where('housing_context', 'general')
            ->get()
            ->map(function ($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'description' => $property->pcontent,
                    'price' => $property->price,
                    'location' => $property->location,
                    'city' => $property->city,
                    'property_type' => $property->property_type,
                    'amenities' => $property->amenities,
                    'availability_status' => $property->availability_status ?? $property->status,
                    'status' => $property->status,
                    'image' => $property->pimage ? asset('storage/' . $property->pimage) : null,
                    'created_at' => $property->created_at,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $properties
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/general/landlord/properties",
     *     tags={"General Housing Landlord"},
     *     summary="Create a new property listing",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "description", "price", "location", "property_type"},
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="price", type="number"),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="city", type="string"),
     *                 @OA\Property(property="property_type", type="string", enum={"room", "cottage", "flat", "house"}),
     *                 @OA\Property(property="amenities", type="string"),
     *                 @OA\Property(property="bedrooms", type="integer"),
     *                 @OA\Property(property="bathrooms", type="integer"),
     *                 @OA\Property(property="size", type="string"),
     *                 @OA\Property(property="main_image", type="string", format="binary"),
     *                 @OA\Property(property="kitchen_image", type="string", format="binary"),
     *                 @OA\Property(property="bathroom_image", type="string", format="binary"),
     *                 @OA\Property(property="outside_image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Property created successfully"
     *     ),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'price'         => 'required|numeric|min:0',
            'location'      => 'required|string|max:255',
            'city'          => 'nullable|string|max:255',
            'property_type' => 'required|string|in:room,cottage,flat,house',
            'amenities'     => 'nullable|string',
            'bedrooms'      => 'nullable|integer|min:0',
            'bathrooms'     => 'nullable|integer|min:0',
            'size'          => 'nullable|string',
            'main_image'    => 'nullable|image|max:5120',
            'kitchen_image' => 'nullable|image|max:5120',
            'bathroom_image'=> 'nullable|image|max:5120',
            'outside_image' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        $mainImagePath = null;
        $kitchenImagePath = null;
        $bathroomImagePath = null;
        $outsideImagePath = null;

        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')->store('properties', 'public');
        }
        if ($request->hasFile('kitchen_image')) {
            $kitchenImagePath = $request->file('kitchen_image')->store('properties', 'public');
        }
        if ($request->hasFile('bathroom_image')) {
            $bathroomImagePath = $request->file('bathroom_image')->store('properties', 'public');
        }
        if ($request->hasFile('outside_image')) {
            $outsideImagePath = $request->file('outside_image')->store('properties', 'public');
        }

        $property = Properties::create([
            'title'               => $request->title,
            'pcontent'            => $request->description,
            'price'               => $request->price,
            'location'            => $request->location,
            'city'                => $request->city,
            'property_type'       => $request->property_type,
            'housing_context'     => 'general',
            'amenities'           => $request->amenities,
            'bedroom'             => $request->bedrooms,
            'bathroom'            => $request->bathrooms,
            'size'                => $request->size,
            'uid'                 => $user->id,
            'status'              => 'Available',
            'availability_status' => 'active',
            'pimage'              => $mainImagePath,
            'pimage1'             => $kitchenImagePath,
            'pimage2'             => $bathroomImagePath,
            'pimage3'             => $outsideImagePath,
            'date'                => Carbon::now(),
            'count'               => 0,
            'likes'               => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Property created successfully',
            'data' => [
                'id' => $property->id,
                'title' => $property->title,
                'property_type' => $property->property_type,
            ]
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/general/landlord/properties/{id}",
     *     tags={"General Housing Landlord"},
     *     summary="Update a property listing",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="property_type", type="string"),
     *             @OA\Property(property="amenities", type="string"),
     *             @OA\Property(property="availability_status", type="string", enum={"active", "inactive"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Property updated"),
     *     @OA\Response(response=404, description="Property not found"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $property = Properties::where('id', $id)
            ->where('uid', $user->id)
            ->where('housing_context', 'general')
            ->first();

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found or access denied'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'               => 'nullable|string|max:255',
            'description'         => 'nullable|string',
            'price'               => 'nullable|numeric|min:0',
            'location'            => 'nullable|string|max:255',
            'city'                => 'nullable|string|max:255',
            'property_type'       => 'nullable|string|in:room,cottage,flat,house',
            'amenities'           => 'nullable|string',
            'availability_status' => 'nullable|string|in:active,inactive',
            'bedrooms'            => 'nullable|integer|min:0',
            'bathrooms'           => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [];
        if ($request->has('title')) $updateData['title'] = $request->title;
        if ($request->has('description')) $updateData['pcontent'] = $request->description;
        if ($request->has('price')) $updateData['price'] = $request->price;
        if ($request->has('location')) $updateData['location'] = $request->location;
        if ($request->has('city')) $updateData['city'] = $request->city;
        if ($request->has('property_type')) $updateData['property_type'] = $request->property_type;
        if ($request->has('amenities')) $updateData['amenities'] = $request->amenities;
        if ($request->has('availability_status')) $updateData['availability_status'] = $request->availability_status;
        if ($request->has('bedrooms')) $updateData['bedroom'] = $request->bedrooms;
        if ($request->has('bathrooms')) $updateData['bathroom'] = $request->bathrooms;

        $property->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Property updated successfully',
            'data' => $property
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/general/landlord/properties/{id}",
     *     tags={"General Housing Landlord"},
     *     summary="Delete a property listing",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Property deleted"),
     *     @OA\Response(response=404, description="Property not found")
     * )
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $property = Properties::where('id', $id)
            ->where('uid', $user->id)
            ->where('housing_context', 'general')
            ->first();

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found or access denied'
            ], 404);
        }

        $property->delete();

        return response()->json([
            'status' => true,
            'message' => 'Property deleted successfully'
        ], 200);
    }
}
