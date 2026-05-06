<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\PayHereService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $subscription = $tenant->currentSubscription;
        $payments = $tenant->payments()->latest()->paginate(10);
        $plans = config('plans', []);

        return view('billing.index', compact('tenant', 'subscription', 'payments', 'plans'));
    }

    public function checkout(Request $request, string $plan)
    {
        $plans = config('plans', []);

        if (! array_key_exists($plan, $plans)) {
            return redirect()->route('billing.index')->withErrors(['plan' => 'Invalid plan selected.']);
        }

        $user = $request->user();
        $tenant = $user->tenant;

        $currentSub = $tenant->currentSubscription;
        if ($currentSub && $currentSub->isActive()) {
            return redirect()->route('billing.index')->withErrors(['subscription' => 'You already have an active subscription. Cancel it first to switch plans.']);
        }

        $payhere = new PayHereService();

        if ($currentSub && $currentSub->isPendingPayment()) {
            $planConfig = $plans[$plan];
            $checkoutData = $payhere->buildCheckoutData($tenant, $currentSub, $planConfig);
            return view('billing.checkout', [
                'checkoutData' => $checkoutData,
                'planName' => $planConfig['name'],
            ]);
        }

        $result = $payhere->createSubscription($tenant, $plan);

        if (! $result) {
            return redirect()->route('billing.index')->withErrors(['subscription' => 'Failed to create subscription.']);
        }

        return view('billing.checkout', [
            'checkoutData' => $result['checkout_data'],
            'planName' => $plans[$plan]['name'],
        ]);
    }

    public function return(Request $request)
    {
        $orderId = $request->query('order_id');

        if ($orderId) {
            $subscription = Subscription::find($orderId);
            if ($subscription && $subscription->isPendingPayment()) {
                return redirect()->route('billing.index')
                    ->with('info', 'Your payment is being processed. You will be notified once confirmed.');
            }
        }

        return redirect()->route('billing.index')->with('success', 'Payment is being processed. You will be notified once confirmed.');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('billing.index')->with('error', 'Payment was cancelled. Your account is awaiting payment.');
    }

    public function cancelSubscription(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $subscription = $tenant->activeSubscription;

        if (! $subscription) {
            return redirect()->route('billing.index')->withErrors(['subscription' => 'No active subscription found.']);
        }

        $payhere = new PayHereService();

        if ($payhere->cancelSubscription($subscription)) {
            return redirect()->route('billing.index')->with('success', 'Subscription cancelled successfully.');
        }

        return redirect()->route('billing.index')->withErrors(['subscription' => 'Failed to cancel subscription.']);
    }

    public function switchPlan(Request $request, string $plan)
    {
        $plans = config('plans', []);

        if (! array_key_exists($plan, $plans)) {
            return redirect()->route('billing.index')->withErrors(['plan' => 'Invalid plan selected.']);
        }

        $user = $request->user();
        $tenant = $user->tenant;
        $currentSubscription = $tenant->activeSubscription;

        if ($currentSubscription) {
            $payhere = new PayHereService();
            $payhere->cancelSubscription($currentSubscription);
        }

        $payhere = new PayHereService();
        $result = $payhere->createSubscription($tenant, $plan);

        if (! $result) {
            return redirect()->route('billing.index')->withErrors(['subscription' => 'Failed to create new subscription.']);
        }

        return view('billing.checkout', [
            'checkoutData' => $result['checkout_data'],
            'planName' => $plans[$plan]['name'],
        ]);
    }
}