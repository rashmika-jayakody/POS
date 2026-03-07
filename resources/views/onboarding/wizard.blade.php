<x-guest-layout>
    <div class="auth-narrow">
        <div class="step-indicator" style="display: flex; gap: 12px; margin-bottom: 28px; justify-content: center;">
            <span class="step-dot {{ ($step ?? 1) === 1 ? 'active' : (($step ?? 1) > 1 ? 'done' : '') }}" id="dot1"></span>
            <span class="step-dot {{ ($step ?? 1) === 2 ? 'active' : (($step ?? 1) > 2 ? 'done' : '') }}" id="dot2"></span>
            <span class="step-dot {{ ($step ?? 1) === 3 ? 'active' : (($step ?? 1) > 3 ? 'done' : '') }}" id="dot3"></span>
        </div>

        <div class="plan-badge" style="display: inline-block; background: linear-gradient(135deg, #4A9EFF 0%, #00C9B7 100%); color: white; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; margin-bottom: 24px; text-align: center; width: 100%;">
            {{ $planInfo['name'] }} — LKR {{ number_format($planInfo['price_lkr']) }}<span style="font-size: 0.85rem; opacity: 0.95;">/month</span>
        </div>

            <form action="{{ route('onboarding.store') }}" method="POST" id="wizardForm">
                @csrf
                <input type="hidden" name="plan" value="{{ $plan }}">

                <div class="wizard-step active" data-step="1">
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

                <div class="wizard-step" data-step="2">
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

                <div class="wizard-step" data-step="3">
                    <h2 class="auth-heading">Your account</h2>
                    <p class="auth-subheading">You'll use this to sign in and manage your store.</p>

                    <div class="form-group">
                        <label for="name">Your name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password">
                        @error('password') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <div class="wizard-actions" style="display: flex; gap: 12px; margin-top: 28px; flex-wrap: wrap;">
                        <button type="button" id="prevStep3" style="flex: 1; min-width: 120px; padding: 13px 20px; background: white; color: #64748b; border: 1.5px solid #e2e8f0; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.15s;">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="submit" class="auth-btn" style="width: auto; flex: 1; min-width: 120px;">
                            <i class="fas fa-check" style="margin-right: 8px;"></i> Create my store
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

        // Step navigation
        document.getElementById('nextStep1').addEventListener('click', function() {
            const selected = document.querySelector('input[name="pos_type"]:checked');
            if (!selected) {
                alert('Please select a POS type');
                return;
            }
            document.querySelector('.wizard-step[data-step="1"]').classList.remove('active');
            document.querySelector('.wizard-step[data-step="2"]').classList.add('active');
            document.getElementById('dot1').classList.remove('active');
            document.getElementById('dot1').classList.add('done');
            document.getElementById('dot2').classList.add('active');
        });

        document.getElementById('nextStep2').addEventListener('click', function() {
            document.querySelector('.wizard-step[data-step="2"]').classList.remove('active');
            document.querySelector('.wizard-step[data-step="3"]').classList.add('active');
            document.getElementById('dot2').classList.remove('active');
            document.getElementById('dot2').classList.add('done');
            document.getElementById('dot3').classList.add('active');
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
    </script>
