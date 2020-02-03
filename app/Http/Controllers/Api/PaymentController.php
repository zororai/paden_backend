<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\regMoney;
use Paynow\Payments\Paynow;

class PaymentController extends Controller
{
    // Validation rules for the payment
    public function rules()
    {
        return [
            'phone' => ['required', 'string', 'max:255'],
            'paymentmethod' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/payment/regpayment",
     *     tags={"RegPayments"},
*     security={{
     *         "bearerAuth": {}
     *     }},
     *     description="Processes a payment through Paynow using mobile money.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "paymentmethod", "amount"},
     *             @OA\Property(property="phone", type="string", example="0771234567"),
     *             @OA\Property(property="paymentmethod", type="string", example="ecocash"),
     *             @OA\Property(property="amount", type="number", format="float", example=10.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transaction successful!"),
     *             @OA\Property(property="reference_number", type="string", example="PN1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Transaction failed or cancelled",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Transaction cancelled"),
     *             @OA\Property(property="action", type="string", example="Please try again or contact support."),
     *             @OA\Property(property="redirect_url", type="string", example="http://your-app.com/payment/retry")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Paynow service unavailable",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Service is down, please try again later.")
     *         )
     *     )
     * )
     */



 public function storepay(Request $request)
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
            sleep(200);

            $status = $paynow->pollTransaction($pollUrl);
            $paynowReference = $status->paynowReference();

            if ($status->paid()) {
                RegMoney::create([
                    'user_id' => auth()->user()->id,
                    'amount' => $request->input('amount'),
                    'reference_number' => $paynowReference,
                ]);

                return response()->json([
                    'message' => 'Transaction successful!',
                    'redirect_url' => url('/api/home'),
                    'reference_number' => $paynowReference,
                ], 200);
            } 
        } else {
            return response()->json(['error' => 'Service is down, please try again later.'], 500);
        }


        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred during payment: ' . $e->getMessage()
            ], 500);
        }
    }

}
