<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Properties;

/**
 * @OA\Tag(
 *     name="Search",
 *     description="Property search functionality"
 * )
 */
class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Ensure you're using the correct middleware for API
    }
/**
 * @OA\Post(
 *     path="/api/search",
 *     tags={"Search"},
 *     summary="Search for properties",
 *     security={{ "bearerAuth": {} }},
 *     description="Search for properties based on a keyword. The search is performed on the 'title' field of properties within the user's university (city).",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"keyword"},
 *             @OA\Property(property="keyword", type="string", example="house"),
 *             @OA\Property(property="min_price", type="integer", example=100000),
 *             @OA\Property(property="max_price", type="integer", example=500000)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Search results",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Search results"),
 *             @OA\Property(property="results", type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Sunset Villa"),
 *                     @OA\Property(property="location", type="string", example="Rose Garden, NY"),
 *                     @OA\Property(property="price", type="integer", example=250000),
 *                     @OA\Property(property="like", type="integer", example=10),
 *                     @OA\Property(property="Fridge", type="string", example="Yes"),
 *                     @OA\Property(property="WaterTank", type="string", example="Yes"),
 *                     @OA\Property(property="Solar", type="string", example="Available"),
 *                     @OA\Property(property="Roommates", type="string", example="2"),
 *                     @OA\Property(property="wifi", type="string", example="High Speed"),
 *                     @OA\Property(property="room_image", type="string", format="url", example="https://example.com/storage/room.jpg")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="No keyword provided"),
 *     @OA\Response(response=404, description="No properties found")
 * )
 */

public function search(Request $request)
{
    $user = auth()->user();
    $keyword = trim($request->input('keyword'));
    $minPrice = $request->input('min_price');
    $maxPrice = $request->input('max_price');

    if (empty($keyword)) {
        return response()->json(['message' => 'No keyword provided'], 400);
    }

    $query = Properties::where('city', $user->university)
        ->where('title', 'LIKE', "%$keyword%");

    // Apply price filters if present
    if ($minPrice !== null && $maxPrice !== null) {
        $query->whereBetween('price', [$minPrice, $maxPrice]);
    } elseif ($minPrice !== null) {
        $query->where('price', '>=', $minPrice);
    } elseif ($maxPrice !== null) {
        $query->where('price', '<=', $maxPrice);
    }

    $results = $query->get();

    if ($results->isEmpty()) {
        return response()->json(['message' => 'No properties found', 'results' => []], 404);
    }

    $properties = $results->map(function ($property) {
        return [
            'id' => $property->id,
            'name' => $property->name,
            'location' => $property->title,
            'price' => $property->price,
            'like' => $property->like,
            'Fridge' => $property->bedroom,
            'WaterTank' => $property->balcony,
            'Solar' => $property->hall,
            'Roommates' => $property->size,
            'wifi' => $property->topmapimage,
            'room_image' => asset('storage/' . $property->room_image),
        ];
    });

    return response()->json([
        'message' => 'Search results',
        'results' => $properties,
    ], 200);
}

     /**
 * @OA\Get(
 *     path="/api/properties/bylocation",
 *     tags={"Search"},
 *     summary="Get properties for authenticated user's university",
 *     description="Returns a list of properties filtered by the user's university (city).",
 *     operationId="getPropertiesByUniversity",
 *     security={{
     *         "bearerAuth": {}
     *     }},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                
 *                 @OA\Property(property="location", type="string", example="Zills Home"),
 *            
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
public function getPropertiesByUniversity()
{
    $user = auth()->user();

    $properties = Properties::where('city', $user->university)->get();
    return response()->json($properties, 200);
}

     
}
