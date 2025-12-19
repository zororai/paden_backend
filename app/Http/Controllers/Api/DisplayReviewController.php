<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Properties;
use Illuminate\Http\Request;
/**
 * @OA\Schema(
 *     schema="Review",
 *     description="A user review for a property",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="properties_id", type="integer"),
 *     @OA\Property(property="comment", type="string"),
 *     @OA\Property(property="Rating", type="integer", description="Rating from 1 to 5"),
 *     @OA\Property(property="flag", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class DisplayReviewController extends Controller
{
    /**
     * Get all reviews for a specific property.
     *
     * @OA\Get(
     *     path="/api/reviews/{id}",
     *     tags={"View-review-for-specific-property"},
     *     summary="Get reviews for a specific property",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     description="Fetch all reviews for a given property.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the property to fetch reviews for",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved reviews",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="property_id", type="integer"),
     *             @OA\Property(property="reviews", type="array", 
     *                 @OA\Items(ref="#/components/schemas/Review")
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
    public function index($id)
    {
        // Fetch reviews for the specified property
        $reviews = Review::where('properties_id', $id)->get();

        if ($reviews->isEmpty()) {
            return response()->json(['error' => 'No reviews found for this property'], 404);
        }

        return response()->json([
            'property_id' => $id,
            'reviews' => $reviews
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/reviews/store",
     *     tags={"User-write-review"},
     *     summary="Store a new review for a property",
  *     security={{
     *         "bearerAuth": {}
     *     }},
     *     description="Allow users to add a review for a specific property.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id", "comment", "review"},
     *             @OA\Property(property="property_id", type="integer"),
     *             @OA\Property(property="comment", type="string"),
     *             @OA\Property(property="review", type="integer", description="Rating between 1 and 5")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully added review",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="review", ref="#/components/schemas/Review")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data provided",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function storeReviews(Request $request)
    {
        $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'comment' => 'required|string|max:255',
            'review' => 'required|integer|min:1|max:5', // Assuming a rating scale of 1 to 5
        ]);

        $userId = auth()->user()->id;

        // Check if the user has already added a review for the property
        $existingReview = Review::where('user_id', $userId)
            ->where('properties_id', $request->input('property_id'))
            ->first();

        if ($existingReview) {
            return response()->json(['error' => 'You have already added a review'], 400);
        }

        // Create the review
        $review = Review::create([
            'user_id' => $userId,
            'properties_id' => $request->input('property_id'),
            'comment' => $request->input('comment'),
            'flag' => 1, // Assuming a default flag value
            'Rating' => $request->input('review'),
        ]);

        return response()->json(['message' => 'Review successfully added!', 'review' => $review], 201);
    }



/**
 * @OA\Delete(
 *     path="/api/reviews/delete/{reviewId}",
 *     tags={"Delete-written-review"},
 *     summary="Delete a review by review ID (admin or general delete)",
*     security={{
     *         "bearerAuth": {}
     *     }},
 *     description="Delete any review by its review ID (admin or moderator functionality).",
 *     @OA\Parameter(
 *         name="reviewId",
 *         in="path",
 *         required=true,
 *         description="The ID of the review to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully deleted review",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string")
 *         )
 *     )
 * )
 */
public function deleteReviewById($reviewId)
{
    $review = Review::find($reviewId);

    if (!$review) {
        return response()->json(['error' => 'Review not found'], 404);
    }

    $review->delete();

    return response()->json(['message' => 'Review deleted successfully'], 200);
}
/**
 * @OA\Get(
 *     path="/api/property/{id}/reviews",
 *     tags={"View-property-review"},
 *     summary="Get all reviews for a specific property",
  *     security={{
     *         "bearerAuth": {}
     *     }},
 *     description="Fetch all reviews written for a given property by property ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the property to fetch reviews for",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully retrieved list of reviews",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Review")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No reviews found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string")
 *         )
 *     )
 * )
 */
public function getReviewsForProperty($id)
{
    $reviews = Review::where('properties_id', $id)->get();

    if ($reviews->isEmpty()) {
        return response()->json(['error' => 'No reviews found for this property'], 404);
    }

    return response()->json($reviews, 200);
}

/**
 * @OA\Get(
 *     path="/api/property/{id}/rating-summary",
 *     tags={"Property-Rating"},
 *     summary="Get property rating summary with property details",
 *     security={{
 *         "bearerAuth": {}
 *     }},
 *     description="Calculate the total rating score and return property info (image, price, location).",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the property",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully retrieved rating summary",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="image", type="string"),
 *             @OA\Property(property="price", type="number"),
 *             @OA\Property(property="location", type="string"),
 *             @OA\Property(property="total_reviews", type="integer"),
 *             @OA\Property(property="total_rating", type="number"),
 *             @OA\Property(property="expected_rating", type="number"),
 *             @OA\Property(property="rating_percentage", type="number"),
 *             @OA\Property(property="rating_out_of_5", type="number", description="Average rating on a 5-star scale")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Property not found"
 *     )
 * )
 */
public function getRatingSummary($id)
{
    $property = Properties::find($id);

    if (!$property) {
        return response()->json(['error' => 'Property not found'], 404);
    }

    $reviews = Review::where('properties_id', $id)->get();
    $totalReviews = $reviews->count();
    $totalRating = $reviews->sum('Rating');
    $expectedRating = 5 * $totalReviews;
    $ratingPercentage = $totalReviews > 0 ? round(($totalRating / $expectedRating) * 100, 2) : 0;
    $ratingOutOf5 = $totalReviews > 0 ? round($totalRating / $totalReviews, 2) : 0;

    return response()->json([
        'image' => asset('storage/' . $property->pimage),
        'price' => $property->price,
        'location' => $property->title,
        'total_reviews' => $totalReviews,
        'total_rating' => $totalRating,
        'expected_rating' => $expectedRating,
        'rating_percentage' => $ratingPercentage,
        'rating_out_of_5' => $ratingOutOf5,
    ], 200);
}

}
