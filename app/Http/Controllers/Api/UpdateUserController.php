<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Properties;
use App\Models\regMoney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Userupdateprofilepic",
 *     description="User-related operations"
 * )
 */
class UpdateUserController extends Controller
{
    

    /**
     * @OA\Post(
     *     path="/api/upload",
     *     tags={"Upload-user-profile"},
     *     summary="Update user profile",
     *     description="Update user's profile image",
*     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(property="image", type="string", format="binary", description="Profile image to upload")
     * 
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'image' => 'required|image|max:2048',
        ]);

 

     
            /**
     * @var \App\Models\User $user
     */
       $user = Auth::user();
       if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('profile', 'public');
        $user->image = $imagePath;
    }
        $user->image = $imagePath;
        $user->save();

        // Check if the user has regMoney records
        $dataCount = RegMoney::where('user_id', $user->id)->count();

        if ($dataCount > 0) {
            $properties = Properties::where('city', $user->university)->get();

            return response()->json([
                'message' => 'Profile updated successfully.',
                'properties' => $properties,
                'redirect_url' => url('/api/home') // Provide the URL to redirect to
            ], 200);
        } else {
            return response()->json([
                'message' => 'Profile updated successfully. Payment required',
                'redirect_url' => url('/api/payment/regpayment') // Provide the redirect URL
            ], 400);
        
        }
    }


    /**
 * @OA\Post(
 *     path="/api/profile/update",
 *     summary="Update user profile",
 *     description="Allows a logged-in user to update their profile information such as name, surname, email, university, phone, password, image, etc.",
 *     operationId="updateProfile",
 *     tags={"Upload-user-profile"},
*     security={{
     *         "bearerAuth": {}
     *     }},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"phone"},
 *                 @OA\Property(property="name", type="string", example="John"),
 *                 @OA\Property(property="surname", type="string", example="Doe"),
 *                 @OA\Property(property="image", type="file"),
 *                 @OA\Property(property="phone", type="string", example="+123456789"),

 *            
 *   
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Profile updated successfully."),
 *             @OA\Property(property="properties", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="redirect_url", type="string", example="http://yourdomain.com/home")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Profile updated successfully. Payment required.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Profile updated successfully. Payment required."),
 *             @OA\Property(property="redirect_url", type="string", example="http://yourdomain.com/payment/regpayment")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 * )
 */
public function updateProfile(Request $request)
{
    /**
     * @var \App\Models\User $user
     */
    $user = Auth::user();

    // Validate incoming request
    $validatedData = $request->validate([
        'name'       => 'nullable|string|max:255',
        'surname'    => 'nullable|string|max:255',
        'image'      => 'nullable|image|max:2048',
        'phone' => 'required|string|max:15',
    ]);

    // Update user fields
    $user->name = $validatedData['name'] ?? $user->name;
    $user->surname = $validatedData['surname'] ?? $user->surname;
    $user->phone = $validatedData['phone']?? $user->phone;

    // Handle image upload
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('profile', 'public');
        $user->image = $imagePath;
    }

    $user->save();

    return response()->json([
        'message' => 'Profile updated successfully.',
        'properties' => $user,
        'redirect_url' => url('/home'),
    ], 200);
}

 

    
}
