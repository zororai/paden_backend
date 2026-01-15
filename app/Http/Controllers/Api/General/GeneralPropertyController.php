<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="General Housing Properties",
 *     description="Property browsing endpoints for general housing tenants"
 * )
 */
class GeneralPropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/api/general/properties",
     *     tags={"General Housing Properties"},
     *     summary="Get all active general housing properties",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="location",
     *         in="query",
     *         description="Filter by location",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Minimum price filter",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Maximum price filter",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="property_type",
     *         in="query",
     *         description="Filter by property type",
     *         @OA\Schema(type="string", enum={"room", "cottage", "flat", "house"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of properties"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $query = Properties::where('housing_context', 'general')
            ->where('status', 'Available');

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        $properties = $query->with('user:id,name,phone,image')->get()->map(function ($property) {
            return [
                'id' => $property->id,
                'title' => $property->title,
                'description' => $property->pcontent,
                'price' => $property->price,
                'location' => $property->location,
                'city' => $property->city,
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'property_type' => $property->property_type,
                'amenities' => $property->amenities,
                'availability_status' => $property->availability_status ?? $property->status,
                'image' => $property->pimage ? asset('storage/' . $property->pimage) : null,
                'images' => [
                    'main' => $property->pimage ? asset('storage/' . $property->pimage) : null,
                    'kitchen' => $property->pimage1 ? asset('storage/' . $property->pimage1) : null,
                    'bathroom' => $property->pimage2 ? asset('storage/' . $property->pimage2) : null,
                    'outside' => $property->pimage3 ? asset('storage/' . $property->pimage3) : null,
                ],
                'landlord' => $property->user ? [
                    'id' => $property->user->id,
                    'name' => $property->user->name,
                ] : null,
                'created_at' => $property->created_at,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $properties
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/general/properties/{id}",
     *     tags={"General Housing Properties"},
     *     summary="Get a single property details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property details"
     *     ),
     *     @OA\Response(response=404, description="Property not found")
     * )
     */
    public function show($id)
    {
        $property = Properties::where('housing_context', 'general')
            ->where('id', $id)
            ->with('user:id,name,phone,image,whatsapp_enabled')
            ->first();

        if (!$property) {
            return response()->json([
                'status' => false,
                'message' => 'Property not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $property->id,
                'title' => $property->title,
                'description' => $property->pcontent,
                'price' => $property->price,
                'location' => $property->location,
                'city' => $property->city,
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'property_type' => $property->property_type,
                'amenities' => $property->amenities,
                'availability_status' => $property->availability_status ?? $property->status,
                'bedrooms' => $property->bedroom,
                'bathrooms' => $property->bathroom,
                'size' => $property->size,
                'images' => [
                    'main' => $property->pimage ? asset('storage/' . $property->pimage) : null,
                    'kitchen' => $property->pimage1 ? asset('storage/' . $property->pimage1) : null,
                    'bathroom' => $property->pimage2 ? asset('storage/' . $property->pimage2) : null,
                    'outside' => $property->pimage3 ? asset('storage/' . $property->pimage3) : null,
                    'landlord' => $property->pimage4 ? asset('storage/' . $property->pimage4) : null,
                ],
                'landlord' => $property->user ? [
                    'id' => $property->user->id,
                    'name' => $property->user->name,
                    'phone' => $property->user->phone,
                    'image' => $property->user->image ? asset('storage/' . $property->user->image) : null,
                    'whatsapp_enabled' => $property->user->whatsapp_enabled,
                ] : null,
                'created_at' => $property->created_at,
            ]
        ], 200);
    }
}
