<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\PayHereService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayHereWebhookController extends Controller
{
    public function handleNotify(Request $request)
    {
        $data = $request->all();

        Log::info('PayHere IPN received', $data);

        $payhere = new PayHereService();

        if (! $payhere->verifyHash($data)) {
            Log::warning('PayHere IPN hash verification failed', $data);

            return response()->json(['error' => 'Invalid hash'], 403);
        }

        $statusCode = $data['status_code'] ?? '';
        $orderId = $data['order_id'] ?? null;

        $merchantId = $data['merchant_id'] ?? '';
        $orderIdMerchant = $data['order_id'] ?? '';

        Log::info('PayHere IPN verified', [
            'status_code' => $statusCode,
            'order_id' => $orderId,
        ]);

        $tenant = Tenant::where('id', $data['custom_1'] ?? null)->first()
            ?? Tenant::where('email', $data['email'] ?? '')->first();

        if (! $tenant) {
            Log::error('PayHere IPN: Tenant not found', ['email' => $data['email'] ?? '', 'custom_1' => $data['custom_1'] ?? '']);

            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $payment = $payhere->recordPayment($tenant, $data);

        $subscription = $tenant->subscriptions()->where('id', $orderId)->first();

        if (! $subscription) {
            Log::error('PayHere IPN: Subscription not found', ['order_id' => $orderId]);

            return response()->json(['error' => 'Subscription not found'], 404);
        }

        $paymentStatus = $payhere->mapPayhereStatus($statusCode);

        if ($paymentStatus === 'successful') {
            $payhere->activateSubscription($subscription, $data);
            Log::info('PayHere IPN: Subscription activated', ['subscription_id' => $subscription->id]);
        } elseif ($paymentStatus === 'failed') {
            $subscription->update(['status' => 'past_due']);
            Log::warning('PayHere IPN: Payment failed, subscription past_due', ['subscription_id' => $subscription->id]);
        }

        return response()->json(['status' => 'success']);
    }
}