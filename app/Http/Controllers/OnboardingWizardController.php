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
        
        // Determine which step to show based on validation errors
        $step = 1;
        if ($request->has('step')) {
            $step = (int) $request->query('step');
        } elseif (session()->has('errors')) {
            // If there are errors, determine which step has errors
            $errors = session()->get('errors')->getBag('default');
            if ($errors->has('pos_type')) {
                $step = 1;
            } elseif ($errors->has('company_name') || $errors->has('address') || $errors->has('phone')) {
                $step = 2;
            } elseif ($errors->has('name') || $errors->has('email') || $errors->has('password')) {
                $step = 3;
            }
        }
        
        return view('onboarding.wizard', [
            'plan' => $plan,
            'planInfo' => self::PLANS[$plan],
            'plans' => self::PLANS,
            'step' => $step,
        ]);
    }

    public function store(Request $request): RedirectResponse|View
    {
        try {
            $validated = $request->validate([
                'plan' => ['required', 'string', 'in:essential,professional,enterprise'],
                'pos_type' => ['required', 'string', 'in:retail,restaurant'],
                'company_name' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:500'],
                'phone' => ['nullable', 'string', 'max:20'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Determine which step has errors
            $errors = $e->errors();
            $step = 1;
            if (isset($errors['pos_type'])) {
                $step = 1;
            } elseif (isset($errors['company_name']) || isset($errors['address']) || isset($errors['phone'])) {
                $step = 2;
            } elseif (isset($errors['name']) || isset($errors['email']) || isset($errors['password'])) {
                $step = 3;
            }
            
            return redirect()->route('onboarding.index', ['plan' => $request->input('plan', 'professional'), 'step' => $step])
                ->withErrors($e->errors())
                ->withInput();
        }

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
                'pos_type' => $validated['pos_type'],
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

    public function validateStep(Request $request)
    {
        $step = $request->input('step');
        $data = $request->except(['_token', 'step']);

        $rules = [];
        $messages = [];

        switch ($step) {
            case 1:
                $rules = [
                    'pos_type' => ['required', 'string', 'in:retail,restaurant'],
                ];
                break;
            case 2:
                $rules = [
                    'company_name' => ['required', 'string', 'max:255'],
                    'address' => ['required', 'string', 'max:500'],
                    'phone' => ['nullable', 'string', 'max:20'],
                ];
                break;
            case 3:
                $rules = [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                ];
                break;
        }

        $validator = \Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
