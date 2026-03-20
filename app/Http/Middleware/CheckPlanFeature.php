<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (auth()->user()->hasRole('system_owner')) {
            return $next($request);
        }

        $tenant = auth()->user()->tenant;

        if (!$tenant || !$tenant->hasFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Your current plan does not include this feature. Please upgrade your plan.')
                ], 403);
            }

            return redirect()->route('pricing.index')
                ->with('error', __('Your current plan does not include this feature. Please upgrade to access this functionality.'));
        }

        return $next($request);
    }
}
