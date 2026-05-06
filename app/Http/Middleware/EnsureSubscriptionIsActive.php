<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $tenant = $user->tenant;

        if (! $tenant) {
            return $next($request);
        }

        if ($tenant->isSubscribed()) {
            return $next($request);
        }

        if ($tenant->isOnTrial()) {
            return $next($request);
        }

        $graceDays = config('services.payhere_checkout.grace_days', 7);
        $subscription = $tenant->currentSubscription;

        if ($subscription && $subscription->isPendingPayment()) {
            if ($request->routeIs('billing.*') || $request->routeIs('payhere.*') || $request->routeIs('logout') || $request->routeIs('profile.*')) {
                return $next($request);
            }

            return redirect()->route('billing.index')->with('warning', 'Please complete your payment to activate your account.');
        }

        if ($subscription && $subscription->isPastDue()) {
            $pastDueDate = $subscription->ends_at;

            if ($pastDueDate && $pastDueDate->addDays($graceDays)->isFuture()) {
                return $next($request);
            }
        }

        if ($request->routeIs('billing.*') || $request->routeIs('payhere.*') || $request->routeIs('logout') || $request->routeIs('profile.*') || $request->routeIs('onboarding.*')) {
            return $next($request);
        }

        return redirect()->route('billing.index')->with('warning', 'Your subscription has expired. Please renew to continue using the service.');
    }
}