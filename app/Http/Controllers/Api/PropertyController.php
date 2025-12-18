<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Properties",
 *     description="Operations related to properties management"
 * )
 */

class PropertyController extends Controller
{
   /**
     * @OA\Post(
     *     path="/api/properties",
     *     tags={"Properties"},
     *     summary="Store a new property",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *       @OA\MediaType(
     *  mediaType="multipart/form-data",
     *   @OA\Schema(
     *            
     *      
     * 
     *             required={"location", "discription", "number", "Fridge", "Bathroom", "Tank", "stove", "Solar", "parking", "TotalRooms", "Roommates", "Boarding", "Selling", "wifi", "giza", "Gender", "price", "Geo", "University", "room", "Kitchen", "Toilet", "out", "landlord"},
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="discription", type="string"),
     *             @OA\Property(property="number", type="integer"),
     *             @OA\Property(property="Fridge", type="string"),
     *             @OA\Property(property="Bathroom", type="string"),
     *             @OA\Property(property="Tank", type="string"),
     *             @OA\Property(property="stove", type="string"),
     *             @OA\Property(property="Solar", type="string"),
     *             @OA\Property(property="parking", type="string"),
     *             @OA\Property(property="TotalRooms", type="string"),
     *             @OA\Property(property="Roommates", type="string"),
     *             @OA\Property(property="Boarding", type="string"),
     *             @OA\Property(property="Selling", type="string"),
     *             @OA\Property(property="titleland", type="string"),
     *             @OA\Property(property="wifi", type="string"),
     *             @OA\Property(property="giza", type="string"),
     *             @OA\Property(property="Gender", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="Geo", type="string"),
     *             @OA\Property(property="University", type="string"),
     *             @OA\Property(property="room", type="string", format="binary"),
     *             @OA\Property(property="Kitchen", type="string", format="binary"),
     *             @OA\Property(property="Toilet", type="string", format="binary"),
     *             @OA\Property(property="out", type="string", format="binary"),
     *             @OA\Property(property="landlord", type="string", format="binary")
     *         )
     * )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Property added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Property added successfully."),
     *             @OA\Property(property="property", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $flag = "0";
        $Yes = "Available";
        $Feature = "Feature";
        $province = "Available";
        $currentDate = Carbon::now();

        // Validate the incoming request
        $validatedData = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'discription' => 'required|string',
            'number' => 'required|numeric',
            'titleland' => 'required|string|max:255',
            //'name' => 'required|string|max:255',
            'Fridge' => 'required|string|max:255',
            'Bathroom' => 'required|string|max:255',
            'Tank' => 'required|string|max:255',
            'stove' => 'required|string|max:255',
            'Solar' => 'required|string|max:255',
            'parking' => 'required|string|max:255',
            'TotalRooms' => 'required|string|max:255',
            'Roommates' => 'required|string|max:255',
            'Boarding' => 'required|string|max:255',
            'Selling' => 'required|string|max:255',
           //'Status' => 'required|string|max:255',
            'wifi' => 'required|string|max:255',
            'giza' => 'required|string|max:255',
            'Gender' => 'required|string|max:255',
            'price' => 'required|numeric',
            'Geo' => 'required|string|max:255',
            'University' => 'required|string|max:255',
          
            
          'room' => 'required|image',
            'Kitchen' => 'required|image',
           'Toilet' => 'required|image',
           'out' => 'required|image',
           'landlord' => 'required|image',
        ]);

        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(), 400);
        }

        // Store images
     
        
     $image1Path = $request->file('room')->store('properties', 'public');
       $image2Path = $request->file('Kitchen')->store('properties', 'public');
      $image3Path = $request->file('Toilet')->store('properties', 'public');
        $image4Path = $request->file('out')->store('properties', 'public');
        $image5Path = $request->file('landlord')->store('profile', 'public');
//

        // Create a new property
        $property = Properties::create([
            'title' => $request->input('location'),
            'pcontent' => $request->input('discription'),
            'mapimage' => $request->input('number'),
           
            'uid' => auth()->user()->id,
            'pimage4' => $image5Path,
            'bedroom' => $request->input('Fridge'),
            'bathroom' => $request->input('Bathroom'),
            'balcony' => $request->input('Tank'),
            'kitchen' => $request->input('stove'),
            'hall' => $request->input('Solar'),
            'bhk' => $request->input('parking'),
            'floor' => $request->input('TotalRooms'),
            'size' => $request->input('Roommates'),
            'type' => $request->input('Boarding'),
            'stype' => $request->input('Selling'),
            'status' => $Yes,
            'totalfloor' => $request->input('Gender'),
            'pimage' => $image1Path,
            'pimage1' => $image2Path,
            'pimage2' => $image3Path,
            'pimage3' => $image4Path,
            'price' => $request->input('price'),
            'location' => $request->input('Geo'),
            'city' => $request->input('University'),
            'state' =>   $Yes,
            'feature' => $Feature,
            'count' => $flag,
            'like' => $flag,
            'topmapimage' => $request->input('wifi'),
            'groundmapimage' => $request->input('giza'),
            'date' => $currentDate,
            'isFeatured' => $request->input('titleland'),
        ]);

        if ($property) {
            return response()->json(['message' => 'Property added successfully.', 'property' => $property], 201);
        } else {
            return response()->json(['message' => 'Unable to add property.'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/myproperties",
     *     tags={"Properties"},
     *     summary="Get all properties created by the authenticated user",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved properties",
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
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
public function myProperties()
{
    $userId = auth()->user()->id;

    $properties = Properties::where('uid', $userId)->get();

    $transformed = $properties->map(function ($property) {
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

    return response()->json([
        'message' => 'Your properties retrieved successfully.',
        'properties' => $transformed,
    ], 200);
}


/**
     * @OA\Put(
     *     path="/api/properties/{id}",
     *     tags={"Properties"},
     *     summary="Update an existing property",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="discription", type="string"),
     *             @OA\Property(property="number", type="integer"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="room", type="string", format="binary"),
     *             @OA\Property(property="Kitchen", type="string", format="binary"),
     *             @OA\Property(property="Toilet", type="string", format="binary"),
     *             @OA\Property(property="out", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Property updated successfully."),
     *             @OA\Property(property="property", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */

public function update(Request $request, $id)
{
    $user = auth()->user()->id;

    // Find the property and make sure it belongs to the user
    $property = Properties::where('id', $id)->where('uid', $user)->first();

    if (!$property) {
        return response()->json(['message' => 'Property not found or unauthorized'], 404);
    }

    // Validate only the fields you allow updating
    $validatedData = Validator::make($request->all(), [
        'location' => 'sometimes|string|max:255',
        'discription' => 'sometimes|string',
        'number' => 'sometimes|numeric',
        'price' => 'sometimes|numeric',
        'room' => 'sometimes|image',
        // Add other fields you want to allow updating
    ]);

    if ($validatedData->fails()) {
        return response()->json($validatedData->errors(), 400);
    }

    // Update fields
    $property->title = $request->input('location', $property->title);
    $property->pcontent = $request->input('discription', $property->pcontent);
    $property->mapimage = $request->input('number', $property->mapimage);
    $property->price = $request->input('price', $property->price);

    // If a new image is uploaded, replace the old one
    if ($request->hasFile('room')) {
        if ($property->pimage && Storage::disk('public')->exists($property->pimage)) {
            Storage::disk('public')->delete($property->pimage);
        }
        $property->pimage = $request->file('room')->store('properties', 'public');
    }
    
    if ($request->hasFile('Kitchen')) {
        if ($property->pimage1 && Storage::disk('public')->exists($property->pimage1)) {
            Storage::disk('public')->delete($property->pimage1);
        }
        $property->pimage1 = $request->file('Kitchen')->store('properties', 'public');
    }
    
    if ($request->hasFile('Toilet')) {
        if ($property->pimage2 && Storage::disk('public')->exists($property->pimage2)) {
            Storage::disk('public')->delete($property->pimage2);
        }
        $property->pimage2 = $request->file('Toilet')->store('properties', 'public');
    }
    
    if ($request->hasFile('out')) {
        if ($property->pimage3 && Storage::disk('public')->exists($property->pimage3)) {
            Storage::disk('public')->delete($property->pimage3);
        }
        $property->pimage3 = $request->file('out')->store('properties', 'public');
    }
    


    $property->save();

    return response()->json(['message' => 'Property updated successfully', 'property' => $property], 200);
}

/**
     * @OA\Delete(
     *     path="/api/properties/{id}",
     *     tags={"Properties"},
     *     summary="Soft delete a property",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Property ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Property deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found or unauthorized"
     *     )
     * )
     */
public function destroy($id)
{
    $user = auth()->user()->id;

    $property = Properties::where('id', $id)->where('uid', $user)->first();

    if (!$property) {
        return response()->json(['message' => 'Property not found or unauthorized'], 404);
    }

    $property->delete();

    return response()->json(['message' => 'Property deleted successfully.'], 200);
}

}
