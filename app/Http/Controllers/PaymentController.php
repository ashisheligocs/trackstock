<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function handlePaymentCallback(Request $request)
    {
        // Validate the incoming request
        $this->validate($request, [
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        $input = $request->all();

        try {
            $attributes = [
                'razorpay_order_id' => $input['razorpay_order_id'],
                'razorpay_payment_id' => $input['razorpay_payment_id'],
                'razorpay_signature' => $input['razorpay_signature'],
            ];

            $razorpay = new \Razorpay\Razorpay([
                'key_id' => config('services.razorpay.key_id'),
                'key_secret' => config('services.razorpay.key_secret'),
            ]);

            $razorpay->utility->verifyPaymentSignature($attributes);

            // Log successful payment verification
            \Log::info('Payment verification successful for order ' . $input['razorpay_order_id']);

            return response()->json(['status' => 'success', 'message' => 'Payment verification successful']);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Payment verification error: ' . $e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Payment verification failed'], 500);
        }
    }
}

