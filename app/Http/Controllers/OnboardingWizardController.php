<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class OnboardingWizardController extends Controller
{
    private const PLANS = [
        'essential' => ['name' => 'Essential', 'price_lkr' => 35000],
        'professional' => ['name' => 'Professional', 'price_lkr' => 85000],
        'enterprise' => ['name' => 'Enterprise', 'price_lkr' => 175000],
    ];

    public function index(Request $request): View|RedirectResponse
    {
        $plan = $request->query('plan', 'professional');
        if (!array_key_exists($plan, self::PLANS)) {
            $plan = 'professional';
        }
        return view('onboarding.wizard', [
            'plan' => $plan,
            'planInfo' => self::PLANS[$plan],
            'plans' => self::PLANS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', 'string', 'in:essential,professional,enterprise'],
            'company_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $slug = Tenant::generateUniqueSlug($validated['company_name']);

        $user = \DB::transaction(function () use ($validated, $slug) {
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'slug' => $slug,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'status' => 'active',
                'plan' => $validated['plan'],
            ]);

            $branch = Branch::create([
                'tenant_id' => $tenant->id,
                'name' => 'Main Branch',
                'address' => $validated['address'],
                'phone' => $validated['phone'],
            ]);

            BusinessSetting::create([
                'tenant_id' => $tenant->id,
                'business_name' => $validated['company_name'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'currency_code' => 'LKR',
                'currency_symbol' => 'Rs',
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            $user->assignRole('business_owner');
            return $user;
        });

        event(new \Illuminate\Auth\Events\Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome! Your store is ready. Your store link: ' . route('store.landing', ['tenant' => $slug]));
    }
}
