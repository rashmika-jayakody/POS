<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limitType): Response
    {
        if (auth()->user()->hasRole('system_owner')) {
            return $next($request);
        }

        $tenant = auth()->user()->tenant;

        if (!$tenant || !$tenant->isWithinLimit($limitType)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('You have reached the limit for your current plan.')
                ], 403);
            }

            return redirect()->route('pricing.index')
                ->with('error', __('You have reached the limit for your current plan. Please upgrade to add more items.'));
        }

        return $next($request);
    }
}
