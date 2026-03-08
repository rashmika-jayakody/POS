<x-guest-layout>
    <div class="auth-narrow">
        <div class="step-indicator" style="display: flex; gap: 12px; margin-bottom: 28px; justify-content: center;">
            <span class="step-dot {{ ($step ?? 1) === 1 ? 'active' : (($step ?? 1) > 1 ? 'done' : '') }}" id="dot1"></span>
            <span class="step-dot {{ ($step ?? 1) === 2 ? 'active' : (($step ?? 1) > 2 ? 'done' : '') }}" id="dot2"></span>
            <span class="step-dot {{ ($step ?? 1) === 3 ? 'active' : (($step ?? 1) > 3 ? 'done' : '') }}" id="dot3"></span>
            <span class="step-dot {{ ($step ?? 1) === 4 ? 'active' : (($step ?? 1) > 4 ? 'done' : '') }}" id="dot4"></span>
        </div>
        
        @if ($errors->any())
            <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
                <strong><i class="fas fa-exclamation-circle"></i> Please fix the errors below:</strong>
            </div>
        @endif

        <div class="plan-badge" style="display: inline-block; background: linear-gradient(135deg, #4A9EFF 0%, #00C9B7 100%); color: white; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; margin-bottom: 24px; text-align: center; width: 100%;">
            {{ $planInfo['name'] }} — LKR {{ number_format($planInfo['price_lkr']) }}<span style="font-size: 0.85rem; opacity: 0.95;">/month</span>
        </div>

            <form action="{{ route('onboarding.store') }}" method="POST" id="wizardForm">
                @csrf
                <input type="hidden" name="plan" value="{{ $plan }}">
                @php
                    $onboardingData = session('onboarding_data', []);
                    $currentStep = $step ?? 1;
                @endphp
                @if($currentStep === 4)
                    {{-- Always include all form data as hidden fields when on verification step --}}
                    @php
                        $emailValue = $onboardingData['email'] ?? $email ?? old('email', '');
                        // If email is still empty, try to get from the visible input via JavaScript
                    @endphp
                    <input type="hidden" name="pos_type" value="{{ $onboardingData['pos_type'] ?? old('pos_type', '') }}">
                    <input type="hidden" name="company_name" value="{{ $onboardingData['company_name'] ?? old('company_name', '') }}">
                    <input type="hidden" name="address" value="{{ $onboardingData['address'] ?? old('address', '') }}">
                    <input type="hidden" name="phone" value="{{ $onboardingData['phone'] ?? old('phone', '') }}">
                    <input type="hidden" name="name" value="{{ $onboardingData['name'] ?? old('name', '') }}">
                    <input type="hidden" name="email" id="hidden_email" value="{{ $emailValue }}">
                    <input type="hidden" name="email_backup" id="email_backup_hidden" value="{{ $emailValue }}">
                    <input type="hidden" name="password" value="{{ $onboardingData['password'] ?? old('password', '') }}">
                    <input type="hidden" name="password_confirmation" value="{{ $onboardingData['password_confirmation'] ?? old('password_confirmation', '') }}">
                @endif

                <div class="wizard-step {{ ($step ?? 1) === 1 ? 'active' : '' }}" data-step="1">
                    <h2 class="auth-heading">Choose your POS type</h2>
                    <p class="auth-subheading">Select the type of business you're setting up.</p>

                    <div class="pos-type-options">
                        <label class="pos-type-card" for="pos_type_retail">
                            <input type="radio" id="pos_type_retail" name="pos_type" value="retail" {{ old('pos_type', 'retail') === 'retail' ? 'checked' : '' }} required>
                            <i class="fas fa-store"></i>
                            <h3>Retail POS</h3>
                            <p>For retail stores, supermarkets, and shops</p>
                        </label>
                        <label class="pos-type-card" for="pos_type_restaurant">
                            <input type="radio" id="pos_type_restaurant" name="pos_type" value="restaurant" {{ old('pos_type') === 'restaurant' ? 'checked' : '' }} required>
                            <i class="fas fa-utensils"></i>
                            <h3>Restaurant POS</h3>
                            <p>For restaurants, cafes, and food service</p>
                        </label>
                    </div>
                    @error('pos_type') <p class="error">{{ $message }}</p> @enderror

                    <div class="wizard-actions" style="display: flex; gap: 12px; margin-top: 28px; flex-wrap: wrap;">
                        <button type="button" class="auth-btn" id="nextStep1" style="width: auto; flex: 1; min-width: 120px;">
                            Next <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </button>
                        <a href="{{ url('/') }}#packages" style="flex: 1; min-width: 120px; padding: 13px 20px; background: white; color: #64748b; border: 1.5px solid #e2e8f0; border-radius: 10px; font-weight: 600; text-align: center; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s;">
                            Back to pricing
                        </a>
                    </div>
                </div>

                <div class="wizard-step {{ ($step ?? 1) === 2 ? 'active' : '' }}" data-step="2">
                    <h2 class="auth-heading">Business information</h2>
                    <p class="auth-subheading">We'll create your store URL from your company name (e.g. my-store).</p>

                    <div class="form-group">
                        <label for="company_name">Company / Store name *</label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required
                               placeholder="e.g. Green Grocers">
                        <p class="hint">Your store link will be: yourapp.com/app/<strong id="slugPreview">store</strong></p>
                        @error('company_name') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" required placeholder="Full address">
                        @error('address') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. 011 234 5678">
                        @error('phone') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="wizard-actions" style="display: flex; gap: 12px; margin-top: 28px; flex-wrap: wrap;">
                        <button type="button" id="prevStep2" style="flex: 1; min-width: 120px; padding: 13px 20px; background: white; color: #64748b; border: 1.5px solid #e2e8f0; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.15s;">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" class="auth-btn" id="nextStep2" style="width: auto; flex: 1; min-width: 120px;">
                            Next <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </div>

                <div class="wizard-step {{ ($step ?? 1) === 3 ? 'active' : '' }}" data-step="3">
                    <h2 class="auth-heading">Your account</h2>
                    <p class="auth-subheading">You'll use this to sign in and manage your store.</p>

                    <div class="form-group">
                        <label for="name">Your name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', session('onboarding_data.email', '')) }}" required autocomplete="email">
                        @error('email') <p class="error">{{ $message }}</p> @enderror
                        {{-- Always include email as hidden field too for step 4 --}}
                        <input type="hidden" name="email_backup" id="email_backup" value="{{ old('email', session('onboarding_data.email', '')) }}">
                    </div>
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <div class="password-wrapper" style="position: relative;">
                            <input type="password" id="password" name="password" required autocomplete="new-password" style="padding-right: 45px;">
                            <button type="button" class="pw-toggle" tabindex="-1" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; padding: 0; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; z-index: 10;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm password *</label>
                        <div class="password-wrapper" style="position: relative;">
                            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" style="padding-right: 45px;">
                            <button type="button" class="pw-toggle" tabindex="-1" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; padding: 0; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; z-index: 10;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="wizard-actions" style="display: flex; gap: 12px; margin-top: 28px; flex-wrap: wrap;">
                        <button type="button" id="prevStep3" style="flex: 1; min-width: 120px; padding: 13px 20px; background: white; color: #64748b; border: 1.5px solid #e2e8f0; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.15s;">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" class="auth-btn" id="nextStep3" style="width: auto; flex: 1; min-width: 120px;">
                            Next <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </div>

                <div class="wizard-step {{ ($step ?? 1) === 4 ? 'active' : '' }}" data-step="4">
                    <h2 class="auth-heading">Verify your email</h2>
                    <p class="auth-subheading">We've sent a verification code to <strong id="email-display">{{ $email ?? session('onboarding_data.email') ?? old('email') }}</strong></p>
                    
                    @if(session('success'))
                        <div style="background: #d1fae5; border: 1px solid #86efac; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    {{-- CRITICAL: Always include email as hidden field on step 4 --}}
                    @php
                        $step4Email = $email ?? session('onboarding_data.email') ?? old('email', '');
                    @endphp
                    <input type="hidden" name="email" id="step4_email" value="{{ $step4Email }}" data-email="{{ $step4Email }}">

                    <div class="form-group">
                        <label for="verification_code">Verification Code *</label>
                        <input type="text" id="verification_code" name="verification_code" value="{{ old('verification_code') }}" 
                               required maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="000000"
                               style="text-align: center; font-size: 1.5rem; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                        <p class="hint" style="text-align: center; margin-top: 8px;">Enter the 6-digit code sent to your email</p>
                        @error('verification_code') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div style="text-align: center; margin: 20px 0;">
                        <button type="button" id="resendCodeBtn" style="background: none; border: none; color: #4A9EFF; cursor: pointer; text-decoration: underline; font-size: 0.9rem;">
                            <i class="fas fa-redo"></i> Resend code
                        </button>
                    </div>

                    <div class="wizard-actions" style="display: flex; gap: 12px; margin-top: 28px; flex-wrap: wrap;">
                        <button type="button" id="prevStep4" style="flex: 1; min-width: 120px; padding: 13px 20px; background: white; color: #64748b; border: 1.5px solid #e2e8f0; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.15s;">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" id="verifyAndCreateBtn" class="auth-btn" form="wizardForm" style="width: auto; flex: 1; min-width: 120px;">
                            <i class="fas fa-check" style="margin-right: 8px;"></i> Verify & Create Store
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-guest-layout>
    <style>
        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e2e8f0;
            transition: background 0.3s;
        }
        .step-dot.active {
            background: #4A9EFF;
        }
        .step-dot.done {
            background: #00C9B7;
        }
        .wizard-step {
            display: none;
        }
        .wizard-step.active {
            display: block;
        }
        .form-group .hint {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 6px;
        }
        .error {
            font-size: 0.85rem;
            color: #ef4444;
            margin-top: 6px;
        }
        .field-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }
        .password-toggle {
            z-index: 10;
        }
        .password-toggle:hover {
            color: #4A9EFF !important;
        }
        .pos-type-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 28px;
        }
        .pos-type-card {
            padding: 24px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
            text-align: center;
        }
        .pos-type-card:hover {
            border-color: #4A9EFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 158, 255, 0.15);
        }
        .pos-type-card.selected {
            border-color: #4A9EFF;
            background: #F0F8FF;
            box-shadow: 0 4px 12px rgba(74, 158, 255, 0.2);
        }
        .pos-type-card i {
            font-size: 2.5rem;
            color: #4A9EFF;
            margin-bottom: 12px;
        }
        .pos-type-card h3 {
            font-size: 1.1rem;
            color: #0A1A3D;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .pos-type-card p {
            font-size: 0.85rem;
            color: #64748b;
        }
        input[type="radio"][name="pos_type"] {
            display: none;
        }
        @media (max-width: 640px) {
            .pos-type-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        // POS type selection
        document.querySelectorAll('input[name="pos_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.pos-type-card').forEach(card => {
                    card.classList.remove('selected');
                });
                if (this.checked) {
                    this.closest('.pos-type-card').classList.add('selected');
                }
            });
        });
        // Initialize selected state
        document.querySelectorAll('input[name="pos_type"]:checked').forEach(radio => {
            radio.closest('.pos-type-card').classList.add('selected');
        });

        // Password toggle - handled by guest layout script, but ensure it works here too
        document.querySelectorAll('.pw-toggle').forEach(function (btn) {
            // Ensure only one icon exists
            var existingIcons = btn.querySelectorAll('i');
            if (existingIcons.length > 1) {
                for (var i = 1; i < existingIcons.length; i++) {
                    existingIcons[i].remove();
                }
            }
            
            var passwordWrapper = btn.parentElement;
            var input = passwordWrapper.querySelector('input[type="password"], input[type="text"]');
            var icon = btn.querySelector('i');
            
            if (!icon) {
                icon = document.createElement('i');
                icon.className = 'fas fa-eye';
                btn.appendChild(icon);
            }
            
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Real-time validation function
        async function validateStep(step) {
            const form = document.getElementById('wizardForm');
            
            // Build FormData manually to ensure all fields are captured
            const formData = new FormData();
            formData.append('step', step);
            
            // Get CSRF token
            const csrfToken = document.querySelector('input[name="_token"]');
            if (csrfToken) formData.append('_token', csrfToken.value);
            
            // Get plan
            const planInput = document.querySelector('input[name="plan"]');
            if (planInput) formData.append('plan', planInput.value);
            
            // Step 1 fields
            if (step === 1) {
                const posTypeInput = document.querySelector('input[name="pos_type"]:checked');
                if (posTypeInput) formData.append('pos_type', posTypeInput.value);
            }
            
            // Step 2 fields
            if (step === 2) {
                const companyNameInput = document.getElementById('company_name');
                const addressInput = document.getElementById('address');
                const phoneInput = document.getElementById('phone');
                if (companyNameInput) formData.append('company_name', companyNameInput.value);
                if (addressInput) formData.append('address', addressInput.value);
                if (phoneInput) formData.append('phone', phoneInput.value);
            }
            
            // Step 3 fields
            if (step === 3) {
                const nameInput = document.getElementById('name');
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const passwordConfirmationInput = document.getElementById('password_confirmation');
                
                if (nameInput) formData.append('name', nameInput.value);
                if (emailInput) formData.append('email', emailInput.value.trim().toLowerCase());
                if (passwordInput) formData.append('password', passwordInput.value);
                if (passwordConfirmationInput) formData.append('password_confirmation', passwordConfirmationInput.value);
                
                // Also include step 1 and 2 fields for step 3 validation
                const posTypeInput = document.querySelector('input[name="pos_type"]:checked');
                const companyNameInput = document.getElementById('company_name');
                const addressInput = document.getElementById('address');
                const phoneInput = document.getElementById('phone');
                
                if (posTypeInput) formData.append('pos_type', posTypeInput.value);
                if (companyNameInput) formData.append('company_name', companyNameInput.value);
                if (addressInput) formData.append('address', addressInput.value);
                if (phoneInput) formData.append('phone', phoneInput.value);
                
                console.log('Step 3 validation - Email value:', emailInput?.value);
            }

            try {
                const response = await fetch('{{ route("onboarding.validate-step") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                // Clear previous errors
                document.querySelectorAll('.error').forEach(el => el.remove());
                document.querySelectorAll('.field-error').forEach(el => el.classList.remove('field-error'));

                if (!data.success) {
                    // Display errors
                    Object.keys(data.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"], #${field}`);
                        if (input) {
                            input.classList.add('field-error');
                            const errorDiv = document.createElement('p');
                            errorDiv.className = 'error';
                            errorDiv.textContent = data.errors[field][0];
                            input.parentElement.appendChild(errorDiv);
                        }
                    });
                    return false;
                }
                return true;
            } catch (error) {
                console.error('Validation error:', error);
                return false;
            }
        }

        // Step navigation with validation
        document.getElementById('nextStep1').addEventListener('click', async function() {
            const selected = document.querySelector('input[name="pos_type"]:checked');
            if (!selected) {
                alert('Please select a POS type');
                return;
            }
            
            const isValid = await validateStep(1);
            if (!isValid) return;

            document.querySelector('.wizard-step[data-step="1"]').classList.remove('active');
            document.querySelector('.wizard-step[data-step="2"]').classList.add('active');
            document.getElementById('dot1').classList.remove('active');
            document.getElementById('dot1').classList.add('done');
            document.getElementById('dot2').classList.add('active');
        });

        document.getElementById('nextStep2').addEventListener('click', async function() {
            const isValid = await validateStep(2);
            if (!isValid) return;

            document.querySelector('.wizard-step[data-step="2"]').classList.remove('active');
            document.querySelector('.wizard-step[data-step="3"]').classList.add('active');
            document.getElementById('dot2').classList.remove('active');
            document.getElementById('dot2').classList.add('done');
            document.getElementById('dot3').classList.add('active');
        });

        document.getElementById('nextStep3')?.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const isValid = await validateStep(3);
            if (!isValid) return;

            // Get all form fields explicitly
            const form = document.getElementById('wizardForm');
            const emailInput = document.getElementById('email');
            const nameInput = document.getElementById('name');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const posTypeInput = document.querySelector('input[name="pos_type"]:checked');
            const companyNameInput = document.getElementById('company_name');
            const addressInput = document.getElementById('address');
            const phoneInput = document.getElementById('phone');
            const planInput = document.querySelector('input[name="plan"]');
            
            // Verify all required fields have values
            if (!emailInput || !emailInput.value || emailInput.value.trim() === '') {
                alert('Email is required. Please enter your email address.');
                emailInput?.focus();
                return;
            }
            
            if (!nameInput || !nameInput.value || nameInput.value.trim() === '') {
                alert('Name is required. Please enter your name.');
                nameInput?.focus();
                return;
            }
            
            if (!passwordInput || !passwordInput.value) {
                alert('Password is required. Please enter your password.');
                passwordInput?.focus();
                return;
            }
            
            if (!passwordConfirmationInput || !passwordConfirmationInput.value) {
                alert('Password confirmation is required. Please confirm your password.');
                passwordConfirmationInput?.focus();
                return;
            }
            
            // Build FormData manually to ensure all fields are included
            const formData = new FormData();
            
            // Add all fields explicitly
            if (planInput) formData.append('plan', planInput.value);
            if (posTypeInput) formData.append('pos_type', posTypeInput.value);
            if (companyNameInput) formData.append('company_name', companyNameInput.value);
            if (addressInput) formData.append('address', addressInput.value);
            if (phoneInput) formData.append('phone', phoneInput.value);
            if (nameInput) formData.append('name', nameInput.value);
            if (emailInput) formData.append('email', emailInput.value.trim().toLowerCase());
            if (passwordInput) formData.append('password', passwordInput.value);
            if (passwordConfirmationInput) formData.append('password_confirmation', passwordConfirmationInput.value);
            
            // Add CSRF token
            const csrfToken = document.querySelector('input[name="_token"]');
            if (csrfToken) formData.append('_token', csrfToken.value);
            
            // Log form data for debugging
            console.log('Submitting step 3 with email:', emailInput.value);
            console.log('All FormData entries:', Array.from(formData.entries()));
            
            try {
                const response = await fetch('{{ route("onboarding.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    redirect: 'manual'
                });

                console.log('Response status:', response.status);
                console.log('Response URL:', response.url);

                // Check response status
                if (response.status === 422) {
                    // Validation error
                    try {
                        const data = await response.json();
                        if (data.errors) {
                            console.error('Validation errors:', data.errors);
                            // Show errors on page
                            Object.keys(data.errors).forEach(field => {
                                const errorMsg = data.errors[field][0];
                                const input = document.querySelector(`[name="${field}"], #${field}`);
                                if (input) {
                                    input.classList.add('field-error');
                                    const errorDiv = document.createElement('p');
                                    errorDiv.className = 'error';
                                    errorDiv.textContent = errorMsg;
                                    input.parentElement.appendChild(errorDiv);
                                }
                            });
                            return;
                        }
                    } catch (e) {
                        console.error('Could not parse JSON error response');
                        window.location.reload();
                    }
                } else if (response.status >= 300 && response.status < 400) {
                    // Redirect response - get Location header
                    const location = response.headers.get('Location');
                    if (location) {
                        console.log('Redirecting to:', location);
                        window.location.href = location;
                        return;
                    }
                } else if (response.ok) {
                    // Success - should be JSON with redirect
                    const contentType = response.headers.get('content-type') || '';
                    console.log('Response content-type:', contentType);
                    
                    if (contentType.includes('application/json')) {
                        try {
                            const data = await response.json();
                            console.log('Response data:', data);
                            if (data.redirect) {
                                console.log('Redirecting to step 4:', data.redirect);
                                window.location.href = data.redirect;
                                return;
                            }
                        } catch (e) {
                            console.error('Could not parse JSON response:', e);
                        }
                    }
                    
                    // Fallback: redirect to step 4 manually
                    const plan = planInput ? planInput.value : 'professional';
                    const redirectUrl = `/onboarding?plan=${plan}&step=4`;
                    console.log('Fallback: Redirecting to step 4:', redirectUrl);
                    window.location.href = redirectUrl;
                } else {
                    // Other error - redirect to step 4 anyway
                    const plan = planInput ? planInput.value : 'professional';
                    const redirectUrl = `/onboarding?plan=${plan}&step=4`;
                    console.log('Error occurred, redirecting to step 4:', redirectUrl);
                    window.location.href = redirectUrl;
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                // On error, redirect to step 4
                const plan = planInput ? planInput.value : 'professional';
                const redirectUrl = `/onboarding?plan=${plan}&step=4`;
                console.log('Exception occurred, redirecting to step 4:', redirectUrl);
                window.location.href = redirectUrl;
            }
        });

        document.getElementById('prevStep2').addEventListener('click', function() {
            document.querySelector('.wizard-step[data-step="2"]').classList.remove('active');
            document.querySelector('.wizard-step[data-step="1"]').classList.add('active');
            document.getElementById('dot2').classList.remove('active');
            document.getElementById('dot1').classList.add('active');
            document.getElementById('dot1').classList.remove('done');
        });

        document.getElementById('prevStep3').addEventListener('click', function() {
            document.querySelector('.wizard-step[data-step="3"]').classList.remove('active');
            document.querySelector('.wizard-step[data-step="2"]').classList.add('active');
            document.getElementById('dot3').classList.remove('active');
            document.getElementById('dot2').classList.add('active');
            document.getElementById('dot2').classList.remove('done');
        });

        document.getElementById('prevStep4')?.addEventListener('click', function() {
            document.querySelector('.wizard-step[data-step="4"]').classList.remove('active');
            document.querySelector('.wizard-step[data-step="3"]').classList.add('active');
            document.getElementById('dot4').classList.remove('active');
            document.getElementById('dot3').classList.add('active');
            document.getElementById('dot3').classList.remove('done');
        });
        function slugify(s) {
            return s.toString().toLowerCase().trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .substring(0, 40) || 'store';
        }
        document.getElementById('company_name').addEventListener('input', function() {
            document.getElementById('slugPreview').textContent = slugify(this.value) || 'store';
        });

        // Auto-format verification code input
        const verificationCodeInput = document.getElementById('verification_code');
        if (verificationCodeInput) {
            verificationCodeInput.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                // Limit to 6 digits
                if (this.value.length > 6) {
                    this.value = this.value.substring(0, 6);
                }
            });
        }

        // Verify & Create Store: explicit click submit so button always works on step 4
        document.getElementById('verifyAndCreateBtn')?.addEventListener('click', function() {
            const form = document.getElementById('wizardForm');
            const activeStep = document.querySelector('.wizard-step.active');
            if (!form || !activeStep || activeStep.dataset.step !== '4') return;
            var codeEl = document.getElementById('verification_code');
            if (!codeEl || !codeEl.value || codeEl.value.trim().length !== 6) {
                alert('Please enter the 6-digit verification code from your email.');
                codeEl?.focus();
                return;
            }
            var emailEl = document.getElementById('step4_email') || document.getElementById('hidden_email');
            if (emailEl) {
                var email = (document.getElementById('step4_email')?.value || document.getElementById('hidden_email')?.value || '').trim();
                if (!email) {
                    alert('Email is missing. Please go back to step 3 and enter your email.');
                    return;
                }
            }
            form.submit();
        });

        // Resend code (no nested form - was breaking "Verify & Create Store" submit)
        document.getElementById('resendCodeBtn')?.addEventListener('click', async function() {
            const btn = this;
            const email = document.getElementById('step4_email')?.value || document.getElementById('hidden_email')?.value;
            if (!email) {
                alert('Email not found. Please go back to step 3 and enter your email.');
                return;
            }
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 4px;"></i> Sending...';
            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('email', email);
                const r = await fetch('{{ route("onboarding.resend-code") }}', { method: 'POST', body: formData });
                if (r.ok) {
                    const url = new URL(r.url);
                    if (url.searchParams.get('resent')) {
                        alert('A new code has been sent to your email.');
                    }
                }
            } catch (e) {
                alert('Could not resend code. Please try again.');
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-redo"></i> Resend code';
        });

        // Ensure all form data is included when submitting from step 4
        const wizardForm = document.getElementById('wizardForm');
        if (wizardForm) {
            wizardForm.addEventListener('submit', function(e) {
                const currentStep = document.querySelector('.wizard-step.active');
                if (currentStep && currentStep.dataset.step === '4') {
                    console.log('Submitting from step 4, ensuring all fields are included...');
                    
                    // Get email from step 3 (visible input) or hidden field
                    const emailInput = document.querySelector('input[type="email"][name="email"]');
                    let hiddenEmail = document.querySelector('input[type="hidden"][name="email"]');
                    
                    // Always ensure email is in hidden field - check multiple sources
                    let emailValue = '';
                    
                    // 1. Check step 4 hidden email field
                    const step4Email = document.getElementById('step4_email');
                    if (step4Email && step4Email.value) {
                        emailValue = step4Email.value;
                        console.log('Email from step4_email field:', emailValue);
                    }
                    
                    // 2. Check visible email input from step 3
                    if (!emailValue && emailInput && emailInput.value) {
                        emailValue = emailInput.value;
                        console.log('Email from visible input:', emailValue);
                    }
                    
                    // 3. Check other hidden email fields
                    if (!emailValue) {
                        const allHiddenEmails = document.querySelectorAll('input[type="hidden"][name="email"]');
                        for (let hidden of allHiddenEmails) {
                            if (hidden.value) {
                                emailValue = hidden.value;
                                console.log('Email from hidden field:', emailValue);
                                break;
                            }
                        }
                    }
                    
                    // 4. Ensure email is set in all hidden fields
                    if (emailValue) {
                        // Update step4_email
                        if (step4Email) {
                            step4Email.value = emailValue;
                        }
                        
                        // Update or create hidden_email
                        if (!hiddenEmail) {
                            hiddenEmail = document.createElement('input');
                            hiddenEmail.type = 'hidden';
                            hiddenEmail.name = 'email';
                            hiddenEmail.id = 'hidden_email';
                            this.appendChild(hiddenEmail);
                        }
                        hiddenEmail.value = emailValue;
                        
                        console.log('Final email value set:', emailValue);
                    } else {
                        console.error('CRITICAL: Email not found anywhere!');
                        console.log('Available email fields:', {
                            step4Email: step4Email?.value,
                            emailInput: emailInput?.value,
                            hiddenEmail: hiddenEmail?.value
                        });
                    }
                    
                    // Ensure all required fields from step 3 are included
                    const requiredFields = ['name', 'password', 'password_confirmation', 'pos_type', 'company_name', 'address'];
                    requiredFields.forEach(fieldName => {
                        const fieldInput = document.querySelector(`input[name="${fieldName}"], select[name="${fieldName}"]`);
                        if (fieldInput && fieldInput.value) {
                            let hiddenField = document.querySelector(`input[type="hidden"][name="${fieldName}"]`);
                            if (!hiddenField) {
                                hiddenField = document.createElement('input');
                                hiddenField.type = 'hidden';
                                hiddenField.name = fieldName;
                                this.appendChild(hiddenField);
                            }
                            if (!hiddenField.value) {
                                hiddenField.value = fieldInput.value;
                            }
                        }
                    });
                    
                    // Final check - log all form data
                    const formData = new FormData(this);
                    console.log('Final form data:', Object.fromEntries(formData));
                    
                    // If email is still missing, prevent submission
                    if (!formData.get('email') || formData.get('email').trim() === '') {
                        console.error('Email is still missing! Preventing submission.');
                        e.preventDefault();
                        alert('Email is required. Please go back to step 3 and enter your email address.');
                        return false;
                    }
                }
            });
        }
    </script>
