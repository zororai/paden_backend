<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\University;

/**
 * @OA\Tag(
 *     name="University",
 *     description="University-related operations"
 * )
 */
class UniversityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/my-university",
     *     tags={"University"},

     *     summary="Get the university of the authenticated user",
     *     description="Fetches the university details for the currently authenticated user. This includes the university's name, latitude, and longitude.",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="University details fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="name", type="string", example="Harvard University"),
     *                 @OA\Property(property="latitude", type="number", format="float", example="42.373611"),
     *                 @OA\Property(property="longitude", type="number", format="float", example="-71.109733")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="University not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function getMyUniversity()
    {
        $user = Auth::user();

        // Fetch university details based on the user's university field
        $university = University::where('university', $user->university)->first(['university', 'latitude', 'longitude']);

        if (!$university) {
            return response()->json(['message' => 'University not found'], 404);
        }

        return response()->json([
            'data' => $university
        ]);
    }
}
