<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Directions;
use App\Models\Properties;
use Illuminate\Http\Request;
use Paynow\Payments\Paynow;

/**
 * @OA\Tag(
 *     name="Payments",
 *     description="Paynow Mobile Payments for Directions"
 * )
 */
class DirectionPaymentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/directions/pay/{id}",
     *     tags={"Payments"},
     *     summary="Initiate and confirm Paynow mobile payment",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Property ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "paymentmethod", "amount"},
     *             @OA\Property(property="phone", type="string", example="0771234567"),
     *             @OA\Property(property="paymentmethod", type="string", example="ecocash"),
     *             @OA\Property(property="amount", type="number", format="float", example="5.00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment successful"),
     *             @OA\Property(property="reference", type="string", example="PN123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Payment failed or service down",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Transaction cancelled")
     *         )
     *     )
     * )
     */
    public function pay(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|string|max:255',
            'paymentmethod' => 'required|string|max:255',
            'amount' => 'required|numeric'
        ]);

        try {
            $paynow = new Paynow(
                '16691',
                '98cf77fa-e6bb-4217-9ebb-4468bea7f3c6',
                'http://example.com',
                'http://example.com'
            );

            $user = auth()->user();

            $payment = $paynow->createPayment('IDuser', $user->email);
            $payment->add('Property Direction Payment', $request->amount);
            $payment->setDescription("Student accommodation");

            $response = $paynow->sendMobile($payment, $request->phone, $request->paymentmethod);

            if ($response->success()) {
                $pollUrl = $response->pollUrl();

                sleep(5); // Simulate polling wait (optional: implement better status check)
                $status = $paynow->pollTransaction($pollUrl);

                if ($status->paid()) {
                    $reference = $status->paynowReference();

                    Directions::create([
                        'user_id' => $user->id,
                        'properties_id' => $id,
                        'amount' => $request->amount,
                        'reference_number' => $reference
                    ]);

                    // Optionally update property status (example logic)
                    $property = Properties::find($id);
                    if ($property) {
                        $property->status = 'Not Available';
                        $property->save();
                    }

                    return response()->json([
                        'message' => 'Payment successful',
                        'reference' => $reference
                    ], 200);
                } else {
                    return response()->json([
                        'error' => 'Transaction cancelled or pending'
                    ], 400);
                }
            } else {
                return response()->json([
                    'error' => 'Paynow service is down or failed to initiate payment'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred during payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
