<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $plans = \App\Models\Plan::where('is_active', true)->get();
        
        if (auth()->user()->hasRole('system_owner')) {
            $stats = [];
            foreach ($plans as $plan) {
                $stats[$plan->slug] = \App\Models\Tenant::where('plan', $plan->slug)->count();
            }
            
            return view('pricing.index', [
                'plans' => $plans,
                'stats' => $stats,
                'isSystemOwner' => true,
                'currentPlan' => null,
            ]);
        }

        $currentPlan = auth()->user()->tenant?->plan ?? 'starter';
        
        return view('pricing.index', [
            'plans' => $plans,
            'currentPlan' => $currentPlan,
            'isSystemOwner' => false,
        ]);
    }

    public function update(Request $request, string $slug)
    {
        if (!auth()->user()->hasRole('system_owner')) {
            abort(403);
        }

        $plan = \App\Models\Plan::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_lkr' => 'required|integer|min:0',
            'max_branches' => 'required|integer',
            'max_users' => 'required|integer',
            'features' => 'required|string', // Expecting JSON string from textarea
        ]);

        // Decode features JSON
        $features = json_decode($validated['features'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['features' => 'Invalid JSON format for features.'])->withInput();
        }

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price_lkr' => $validated['price_lkr'],
            'max_branches' => $validated['max_branches'],
            'max_users' => $validated['max_users'],
            'features' => $features,
        ]);

        return redirect()->route('pricing.index')->with('success', 'Plan updated successfully!');
    }
}
