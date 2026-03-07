<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
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
            } elseif ($errors->has('verification_code')) {
                $step = 4;
            }
        }

        // Get email from session if on verification step
        $email = null;
        if ($step === 4) {
            $onboardingData = Session::get('onboarding_data');
            $email = $onboardingData['email'] ?? null;
        }
        
        return view('onboarding.wizard', [
            'plan' => $plan,
            'planInfo' => self::PLANS[$plan],
            'plans' => self::PLANS,
            'step' => $step,
            'email' => $email,
        ]);
    }

    public function store(Request $request): RedirectResponse|View
    {
        // Check if this is just moving to verification step (no verification code yet)
        if (!$request->has('verification_code') || !$request->filled('verification_code')) {
            // Validate all form data first
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

            // Store form data in session and send verification code
            Session::put('onboarding_data', $validated);
            EmailVerificationService::sendCode($validated['email'], 'registration');
            
            return redirect()->route('onboarding.index', [
                'plan' => $request->input('plan', 'professional'),
                'step' => 4
            ])->with('success', 'Verification code has been sent to your email. Please check your inbox.');
        }

        // Verification code is provided, merge with session data if needed
        $onboardingData = Session::get('onboarding_data', []);
        
        // Merge request data with session data (session data takes precedence for form fields, request for verification_code)
        $formData = array_merge($onboardingData, [
            'verification_code' => $request->input('verification_code'),
            'plan' => $request->input('plan', $onboardingData['plan'] ?? 'professional'),
        ]);
        
        // Merge all request data, but session data overrides
        foreach (['pos_type', 'company_name', 'address', 'phone', 'name', 'email', 'password', 'password_confirmation'] as $field) {
            if (!isset($formData[$field]) && $request->has($field)) {
                $formData[$field] = $request->input($field);
            }
        }
        
        // Validate using merged data
        try {
            $validator = \Validator::make($formData, [
                'plan' => ['required', 'string', 'in:essential,professional,enterprise'],
                'pos_type' => ['required', 'string', 'in:retail,restaurant'],
                'company_name' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:500'],
                'phone' => ['nullable', 'string', 'max:20'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'verification_code' => ['required', 'string', 'size:6'],
            ]);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
                $step = 1;
                if ($errors->has('pos_type')) {
                    $step = 1;
                } elseif ($errors->has('company_name') || $errors->has('address') || $errors->has('phone')) {
                    $step = 2;
                } elseif ($errors->has('name') || $errors->has('email') || $errors->has('password')) {
                    $step = 3;
                } elseif ($errors->has('verification_code')) {
                    $step = 4;
                }
                
                return redirect()->route('onboarding.index', ['plan' => $formData['plan'] ?? 'professional', 'step' => $step])
                    ->withErrors($errors)
                    ->withInput();
            }
            
            $validated = $validator->validated();
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
            } elseif (isset($errors['verification_code'])) {
                $step = 4;
            }
            
            return redirect()->route('onboarding.index', ['plan' => $formData['plan'] ?? 'professional', 'step' => $step])
                ->withErrors($e->errors())
                ->withInput();
        }

        // Verify the code
        if (!EmailVerificationService::verify($validated['email'], $validated['verification_code'], 'registration')) {
            return redirect()->route('onboarding.index', [
                'plan' => $validated['plan'] ?? 'professional',
                'step' => 4
            ])->withErrors(['verification_code' => 'Invalid or expired verification code.'])->withInput();
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

        // Clear onboarding session data
        Session::forget('onboarding_data');

        return redirect()->route('dashboard')
            ->with('success', 'Welcome! Your store is ready. Your store link: ' . route('store.landing', ['tenant' => $slug]));
    }

    /**
     * Resend verification code
     */
    public function resendCode(Request $request): RedirectResponse
    {
        $email = $request->input('email');
        
        if (!$email) {
            $onboardingData = Session::get('onboarding_data');
            $email = $onboardingData['email'] ?? null;
        }

        if (!$email) {
            return back()->withErrors(['email' => 'Email address is required.']);
        }

        EmailVerificationService::sendCode($email, 'registration');

        return back()->with('success', 'Verification code has been resent to your email.');
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
            case 4:
                $rules = [
                    'verification_code' => ['required', 'string', 'size:6'],
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
