<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        // if($request)
        $amount = $request->input('amount') * 100;
        // dd($amount);
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
      
        $orderData = [
            'amount'          => $amount,
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $order = $api->order->create($orderData);

        return response()->json(['order' => $order]);
    }

    public function handlePaymentSuccess(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
        try {
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            $response = $payment->capture(['amount' => $payment->amount]);
            Payment::create([
                'customer_name' => $request->input('customer_name'),
                'transaction_id' => $response->id,
                'payment_amount' => $payment->amount / 100,
                'payment_status' => $payment->status,
                'transaction_date' => now()
            ]);

            return response()->json(['status' => 'success', 'message' => 'Payment successfully completed.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Payment verification failed.']);
        }
    }
}
