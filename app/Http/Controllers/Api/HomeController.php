<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Models\regMoney;
use App\Models\User;
use App\Models\Views;
use App\Models\Properties;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Like;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Ensure you're using the correct middleware for API
    }

    /**
     * @OA\Get(
     *     path="/api/home",
     *     tags={"Home"},
     *     security={{"bearerAuth":{}}},
     *     summary="Get home data for authenticated user",
     *     description="Fetch home data for a user, including property listings and payment status.",
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved home data",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="price", type="integer"),
     *                 @OA\Property(property="like", type="integer"),
     *                 @OA\Property(property="Fridge", type="integer"),
     *                 @OA\Property(property="WaterTank", type="stringr"),
     *                 @OA\Property(property="Solar", type="string"),
     *                @OA\Property(property="Roommates", type="string"),
     *                 @OA\Property(property="wifi", type="string"),
     *                 @OA\Property(property="geo", type="string"),
     *                   @OA\Property(property="image", type="string", format="url", example="http://yourdomain.com/storage/properties/room.jpg")
     *
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Payment required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="redirect_url", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
public function index()
{
    $user = auth()->user();

  // Ensure only Students access this
    if ($user->role !== 'Student') {
        return response()->json(['message' => 'zoro'], 201);
    }

    // Check if user needs to upload their image
    if ($user->image === 'new') {
        return response()->json([
            'message' => 'Update your image',
            'user' => $user->only(['id', 'name', 'email']),
            'redirect_url' => url('/upload')
        ], 403);
    }

    // Check if the student has paid the registration fee
    $hasPaid = RegMoney::where('user_id', $user->id)->exists();

    if (!$hasPaid) {
        return response()->json([
            'message' => 'Payment required',
            'user' => $user->only(['id', 'name', 'email']),
            'redirect_url' => url('/payment/regpayment')
        ], 402);
    }

    // Fetch properties from the same city as user's university
    $properties = Properties::where('city', $user->university)->get()->map(function ($property) {
        return [
            'id' => $property->id,
            'name' => $property->isFeatured,
            'location' => $property->title,
            'price' => $property->price,
            'like' => $property->like,
            'Fridge' => $property->bedroom,
            'WaterTank' => $property->balcony,
            'Solar' => $property->hall,
            'Roommates' => $property->size,
            'wifi' => $property->topmapimage,
            'geo' => $property->location,
            'image' => asset('storage/' . $property->pimage),
        ];
    });

    return response()->json($properties, 200);



}




    /**
     * @OA\Get(
     *     path="/api/homedisplay/{id}",
     *     tags={"Home"},
     *     summary="Display details of a property",
   *     security={{
     *         "bearerAuth": {}
     *     }},
     *     description="Display detailed information about a specific property, including views.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Property ID",
     *         @OA\Schema(type="integer")
     *     ),
   *     @OA\Response(
*         response=200,
*         description="Successfully retrieved property details",
*         @OA\JsonContent(
*            type="array",
 *             @OA\Items(
*                 @OA\Property(property="id", type="integer"),
*                 @OA\Property(property="name", type="string"),
*                 @OA\Property(property="location", type="string"),
*                 @OA\Property(property="description", type="string"),
*                 @OA\Property(property="number", type="string"),
*                 @OA\Property(property="titleland", type="string"),
*                 @OA\Property(property="Fridge", type="string"),
*                 @OA\Property(property="Bathroom", type="string"),
*                 @OA\Property(property="Tank", type="string"),
*                 @OA\Property(property="stove", type="string"),
*                 @OA\Property(property="Solar", type="string"),
*                 @OA\Property(property="parking", type="string"),
*                 @OA\Property(property="TotalRooms", type="string"),
*                 @OA\Property(property="Roommates", type="string"),
*                 @OA\Property(property="Boarding", type="string"),
*                 @OA\Property(property="Selling", type="string"),
*                 @OA\Property(property="Gender", type="string"),
*                 @OA\Property(property="wifi", type="string"),
*                 @OA\Property(property="giza", type="string"),
                  @OA\Property(property="uid", type="integer"),
*                 @OA\Property(property="price", type="number"),
*                 @OA\Property(property="Geo", type="string"),
*                 @OA\Property(property="University", type="string"),
*                 @OA\Property(property="landlordsex", type="string"),
*                 @OA\Property(property="like", type="integer"),
*                 @OA\Property(property="room", type="string", format="url",example="http://yourdomain.com/storage/properties/room.jpg"),
*                 @OA\Property(property="Kitchen", type="string", format="url",example="http://yourdomain.com/storage/properties/room.jpg"),
*                 @OA\Property(property="Toilet", type="string", format="url",example="http://yourdomain.com/storage/properties/room.jpg"),
*                 @OA\Property(property="out", type="string", format="url",example="http://yourdomain.com/storage/properties/room.jpg"),
*                 @OA\Property(property="landlord", type="string", format="url" ,example="http://yourdomain.com/storage/properties/room.jpg")
*
*             )
*         )
*     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function displa($id)

    {
        $property = Properties::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

    $formattedProperty = [
        'id' => $property->id,
        'name' => $property->name,
        'location' => $property->title,
        'description' => $property->pcontent,
        'number' => $property->mapimage,
        'titleland' => $property->isFeatured,
        'Fridge' => $property->bedroom,
        'Bathroom' => $property->bathroom,
        'Tank' => $property->balcony,
        'stove' => $property->kitchen,
        'Solar' => $property->hall,
        'parking' => $property->bhk,
        'TotalRooms' => $property->floor,
        'Roommates' => $property->size,
        'Boarding' => $property->type,
        'Selling' => $property->stype,
        'Gender' => $property->totalfloor,
        'wifi' => $property->topmapimage,
        'giza' => $property->groundmapimage,
        'price' => $property->price,
        'Geo' => $property->location,
        'uid' => $property->uid,
        'University' => $property->city,
        'landlordsex' => $property->state,
        'room' => asset('storage/' . $property->pimage),         // Room image
        'Kitchen' => asset('storage/' . $property->pimage1),     // Kitchen image
        'Toilet' => asset('storage/' . $property->pimage2),      // Toilet image
        'out' => asset('storage/' . $property->pimage3),         // Outside image
        'landlord' => asset('storage/' . $property->pimage4)    // Landlord image



    ];

        return response()->json([
            'success' => true,
            'data' => $formattedProperty,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/home/like",
     *     tags={"Home"},
     *     summary="Like a property",
*     security={{
     *         "bearerAuth": {}
     *     }},
     *     description="Allows the authenticated user to like a property.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"properties_id"},
     *             @OA\Property(property="properties_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully liked property",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Already liked",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function like(Request $request)
    {
        $request->validate([
            'properties_id' => 'required|integer|exists:properties,id',
        ]);

        $properties_id = $request->input('properties_id');

        if (Like::where('user_id', Auth::id())->where('properties_id', $properties_id)->exists()) {
            return response()->json(['message' => 'Already liked'], 400);
        } else {
            $like = new Like();
            $like->user_id = Auth::id();
            $like->properties_id = $properties_id;
            $like->save();

            $count = Like::where('properties_id', $properties_id)->count();
            Properties::where('id', $properties_id)->update(['like' => $count]);

            return response()->json(['message' => 'Liked successfully'], 201);
        }
    }
/**
 * @OA\Get(
 *     path="/api/home/popular",
 *     tags={"Home"},
 *     summary="Get popular properties",
 *     security={{"bearerAuth":{}}},
 *     description="Fetch the top 5 popular properties based on likes.",
 *     @OA\Response(
 *         response=200,
 *         description="Successfully retrieved popular properties",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="like", type="integer"),
 *                 @OA\Property(property="location", type="string"),
 *                 @OA\Property(property="price", type="integer"),
 *                 @OA\Property(property="Solar", type="string"),
 *                 @OA\Property(property="Roommates", type="string"),
 *                 @OA\Property(property="wifi", type="string"),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="url",
 *                     example="http://yourdomain.com/storage/properties/room.jpg"
 *                 )
 *             )
 *         )
 *     )
 * )
 */
public function getPopularPropertyIds()
{
    $user = auth()->user();

    $properties = Properties::withCount('likes')
        ->where('city', $user->university)
        ->orderBy('likes_count', 'desc')
        ->take(5)
        ->get();

    // Fallback to global top 5 if user's city has no results
    if ($properties->isEmpty()) {
        $properties = Properties::withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->take(5)
            ->get();
    }

    $response = $properties->map(function ($property) {
        return [
            'id' => $property->id,
            'name' => $property->name,
            'like' => $property->likes_count,
            'location' => $property->title,
            'price' => $property->price,
            'Solar' => $property->hall,
            'Roommates' => $property->size,
            'wifi' => $property->topmapimage,
            'image' => asset('storage/' . $property->pimage),
        ];
    });

    return response()->json($response, 200);
}

/**
 * @OA\Get(
 *     path="/api/home/gethome",
 *     tags={"Home"},
 *     summary="Get popular properties",
 *     security={{"bearerAuth":{}}},
 *     description="Fetch the top 5 popular properties based on likes.",
 *     @OA\Response(
 *         response=200,
 *         description="Successfully retrieved popular properties",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="like", type="integer"),
 *                 @OA\Property(property="location", type="string"),
 *                 @OA\Property(property="price", type="integer"),
 *                 @OA\Property(property="Solar", type="string"),
 *                 @OA\Property(property="Roommates", type="string"),
 *                 @OA\Property(property="wifi", type="string"),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="url",
 *                     example="http://yourdomain.com/storage/properties/room.jpg"
 *                 )
 *             )
 *         )
 *     )
 * )
 */
public function gethome()
{
    $user = auth()->user();

   $properties = Properties::where('city', $user->university)->get()->map(function ($property) {
            return [
            'id' => $property->id,
            'name' => $property->name,
            'like' => $property->likes_count,
            'location' => $property->title,
            'price' => $property->price,
            'Solar' => $property->hall,
            'Roommates' => $property->size,
            'wifi' => $property->topmapimage,
            'image' => asset('storage/' . $property->pimage),
        ];
    });

    return response()->json($response, 200);
}



}
