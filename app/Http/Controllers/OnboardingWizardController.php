<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\EmailVerificationService;
use App\Services\PayHereService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class OnboardingWizardController extends Controller
{
    public static function plans(): array
    {
        return config('plans', []);
    }

    public function index(Request $request): View|RedirectResponse
    {
        $plans = self::plans();
        $plan = $request->query('plan', 'professional');
        if (!array_key_exists($plan, $plans)) {
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
            'planInfo' => $plans[$plan],
            'plans' => $plans,
            'step' => $step,
            'email' => $email,
        ]);
    }

    public function store(Request $request): RedirectResponse|View|JsonResponse
    {
        // Check if this is just moving to verification step (no verification code yet)
        if (!$request->has('verification_code') || !$request->filled('verification_code')) {
            // Log incoming request data for debugging
            \Log::info('Onboarding Step 3 Submit - Request Data:', $request->all());
            \Log::info('Onboarding Step 3 Submit - Email in request:', ['email' => $request->input('email')]);
            
            // Validate all form data first
            try {
                $validated = $request->validate([
                    'plan' => ['required', 'string', 'in:' . implode(',', array_keys(self::plans()))],
                    'pos_type' => ['required', 'string', 'in:retail,restaurant'],
                    'company_name' => ['required', 'string', 'max:255'],
                    'address' => ['required', 'string', 'max:500'],
                    'phone' => ['nullable', 'string', 'max:20'],
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                ]);
                
                \Log::info('Onboarding Step 3 Submit - Validation passed:', ['email' => $validated['email'] ?? 'NOT SET']);
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Log validation errors
                \Log::error('Onboarding Step 3 Submit - Validation failed:', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all(),
                    'email_in_request' => $request->input('email'),
                    'email_backup_in_request' => $request->input('email_backup'),
                ]);
                
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
                
                // If it's an AJAX request, return JSON response
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $e->errors(),
                        'message' => 'Validation failed'
                    ], 422);
                }
                
                return redirect()->route('onboarding.index', ['plan' => $request->input('plan', 'professional'), 'step' => $step])
                    ->withErrors($e->errors())
                    ->withInput();
            }

            // Store form data in session and send verification code
            // Make sure to store all data including password confirmation
            $sessionData = array_merge($validated, [
                'password_confirmation' => $request->input('password_confirmation'),
                'plan' => $request->input('plan', 'professional'),
            ]);
            
            // Debug: Log what we're saving
            \Log::info('Onboarding Step 3 - Saving to session:', $sessionData);
            
            Session::put('onboarding_data', $sessionData);
            Session::save(); // Force save session
            
            // Verify session was saved
            $savedData = Session::get('onboarding_data', []);
            \Log::info('Onboarding Step 3 - Verified session data:', $savedData);
            
            try {
                EmailVerificationService::sendCode($validated['email'], 'registration');
                \Log::info('Onboarding Step 3 - Verification code sent to:', ['email' => $validated['email']]);
            } catch (\Exception $e) {
                \Log::error('Onboarding Step 3 - Failed to send verification code:', ['error' => $e->getMessage()]);
                // Continue anyway - user can resend code
            }
            
            // Always return JSON for AJAX requests, otherwise redirect
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                $redirectUrl = route('onboarding.index', [
                    'plan' => $request->input('plan', 'professional'),
                    'step' => 4
                ]);
                
                \Log::info('Onboarding Step 3 - Returning JSON redirect:', ['url' => $redirectUrl]);
                
                return response()->json([
                    'success' => true,
                    'redirect' => $redirectUrl,
                    'message' => 'Verification code has been sent to your email.'
                ]);
            }
            
            \Log::info('Onboarding Step 3 - Returning HTML redirect to step 4');
            
            return redirect()->route('onboarding.index', [
                'plan' => $request->input('plan', 'professional'),
                'step' => 4
            ])->with('success', 'Verification code has been sent to your email. Please check your inbox.');
        }

        // Verification code is provided, merge with session data if needed
        $onboardingData = Session::get('onboarding_data', []);
        
        // Debug: Log session data
        \Log::info('Onboarding Step 4 - Session Data:', $onboardingData);
        \Log::info('Onboarding Step 4 - Request Data:', $request->all());
        \Log::info('Onboarding Step 4 - Request Email:', ['email' => $request->input('email')]);
        \Log::info('Onboarding Step 4 - Request Email Backup:', ['email_backup' => $request->input('email_backup')]);
        
        // Get email from request FIRST (most reliable)
        $emailFromRequest = $request->input('email');
        if (empty($emailFromRequest)) {
            $emailFromRequest = $request->input('email_backup');
        }
        
        // Merge request data with session data
        // Priority: 1. Request data (for verification_code and email), 2. Session data, 3. Request fallback
        $formData = [
            'verification_code' => $request->input('verification_code'),
            'plan' => $request->input('plan', $onboardingData['plan'] ?? 'professional'),
            'email' => $emailFromRequest ?? $onboardingData['email'] ?? old('email') ?? '',
        ];
        
        // Add all other fields from session data first, then request
        foreach (['pos_type', 'company_name', 'address', 'phone', 'name', 'password', 'password_confirmation'] as $field) {
            // Use session data if available, otherwise use request data
            $formData[$field] = $onboardingData[$field] ?? $request->input($field, '');
        }
        
        // Final email check - ensure it's not empty
        if (empty($formData['email']) || trim($formData['email']) === '') {
            // Try one more time from all possible sources
            $formData['email'] = $request->input('email') 
                ?? $request->input('email_backup')
                ?? $request->input('step4_email')
                ?? $onboardingData['email'] 
                ?? old('email')
                ?? '';
        }
        
        // Final check - if email is still missing, redirect back with error
        if (empty($formData['email']) || trim($formData['email']) === '') {
            \Log::error('Onboarding Step 4 - Email is missing!', [
                'session_data' => $onboardingData,
                'request_data' => $request->all(),
                'form_data' => $formData,
                'email_value' => $formData['email'] ?? 'NOT SET'
            ]);
            
            return redirect()->route('onboarding.index', [
                'plan' => $request->input('plan', 'professional'),
                'step' => 3
            ])->withErrors(['email' => 'Email is required. Please go back and enter your email address.'])->withInput();
        }
        
        // Log successful email capture for debugging
        \Log::info('Onboarding Step 4 - Email found:', ['email' => $formData['email']]);
        
        // Log form data before validation
        \Log::info('Onboarding Step 4 - Form data before validation:', $formData);
        \Log::info('Onboarding Step 4 - Email in formData:', ['email' => $formData['email'] ?? 'NOT SET']);
        
        // Validate using merged data
        try {
            $validator = \Validator::make($formData, [
                'plan' => ['required', 'string', 'in:' . implode(',', array_keys(self::plans()))],
                'pos_type' => ['required', 'string', 'in:retail,restaurant'],
                'company_name' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:500'],
                'phone' => ['nullable', 'string', 'max:20'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
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
                'billing_email' => $validated['email'],
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

        Session::forget('onboarding_data');

        $payhere = new PayHereService();
        $result = $payhere->createSubscription($user->tenant, $validated['plan']);

        if ($result) {
            return view('billing.checkout', [
                'checkoutData' => $result['checkout_data'],
                'planName' => $validated['plan'],
            ]);
        }

        return redirect()->route('billing.index')
            ->with('warning', 'Your account has been created! Please complete your payment to activate your subscription.');
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

        // Log what we're receiving for debugging
        \Log::info("Validate Step {$step} - Request data:", $request->all());
        \Log::info("Validate Step {$step} - Email in request:", ['email' => $request->input('email')]);

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
                // Ensure email is present before validation
                if (empty($data['email']) || trim($data['email']) === '') {
                    \Log::error("Validate Step 3 - Email is missing in data:", $data);
                    return response()->json([
                        'success' => false,
                        'errors' => ['email' => ['The email field is required.']],
                    ], 422);
                }
                
                $rules = [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
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
            \Log::error("Validate Step {$step} - Validation failed:", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        \Log::info("Validate Step {$step} - Validation passed");
        return response()->json([
            'success' => true,
        ]);
    }
}
