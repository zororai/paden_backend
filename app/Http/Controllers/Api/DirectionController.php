<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\Directions;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Properties",
 *     type="object",
 *     title="Property-directions",
 *     description="Property model",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="city", type="string"),
 *     @OA\Property(property="uid", type="integer", description="Agent ID"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="image", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class DirectionController extends Controller
{
    /**
     * Show the details of a property along with agent details if payment is made.
     * 
     * @OA\Get(
     *     path="/api/properties/directions/{id}",
     *     tags={"Property-directions"},
      *     security={{
     *         "bearerAuth": {}
     *     }},
     *     summary="Show directions and agent details if payment is made",
     *     description="This route checks if the user has paid for the property and returns the directions and agent details.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the property",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="house", ref="#/components/schemas/Properties"),
     *             @OA\Property(property="agent_details", ref="#/components/schemas/Properties")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Payment required to view agent details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="house", ref="#/components/schemas/Properties")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $userId = auth()->user()->id;

        // Check if the user has paid for the property
        $paidUserId = Directions::where('properties_id', $id)
            ->where('user_id', $userId)
            ->count();

        $house = Properties::find($id);

        if (!$house) {
            return response()->json(['message' => 'Property not found.'], 404);
        }

        if ($paidUserId === 0) {
            // User has not paid
            return response()->json([
                'message' => 'Payment required to view agent details.',
                'house' => $house
            ], 403);
        } else {
            // User has paid, return agent details
            $agentId = $house->uid;
            $agentDetails = Properties::find($agentId);

            return response()->json([
                'house' => $house,
                'agent_details' => $agentDetails
            ], 200);
        }
    }

}
