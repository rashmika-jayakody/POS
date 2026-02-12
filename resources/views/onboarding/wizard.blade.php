<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Started | POS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-dark: #0A1A3D;
            --light-blue: #4A9EFF;
            --light-blue-bg: #F0F8FF;
            --accent-teal: #00C9B7;
            --white: #FFFFFF;
            --gray-100: #F1F5F9;
            --gray-500: #64748B;
            --gray-900: #0F172A;
            --radius-md: 12px;
            --radius-lg: 20px;
            --shadow-md: 0 8px 24px rgba(10, 26, 61, 0.12);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: var(--gray-100); min-height: 100vh; padding: 40px 20px; }
        .wizard-container { max-width: 520px; margin: 0 auto; }
        .wizard-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 40px;
            margin-bottom: 24px;
        }
        .step-indicator { display: flex; gap: 12px; margin-bottom: 32px; }
        .step-dot {
            width: 12px; height: 12px; border-radius: 50%;
            background: var(--gray-100);
            transition: background 0.3s;
        }
        .step-dot.active { background: var(--light-blue); }
        .step-dot.done { background: var(--accent-teal); }
        .plan-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--light-blue), var(--accent-teal));
            color: var(--white);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }
        .plan-badge .currency { font-size: 0.85rem; opacity: 0.95; }
        h1 { font-size: 1.5rem; color: var(--navy-dark); margin-bottom: 8px; }
        .subtitle { color: var(--gray-500); font-size: 0.95rem; margin-bottom: 28px; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #E2E8F0;
            border-radius: var(--radius-md);
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--light-blue);
            box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.2);
        }
        .form-group .hint { font-size: 0.8rem; color: var(--gray-500); margin-top: 6px; }
        .error { font-size: 0.85rem; color: #DC2626; margin-top: 6px; }
        .wizard-actions { display: flex; gap: 12px; margin-top: 28px; flex-wrap: wrap; }
        .btn {
            padding: 14px 28px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary { background: linear-gradient(135deg, var(--light-blue), var(--accent-teal)); color: var(--white); }
        .btn-primary:hover { opacity: 0.95; transform: translateY(-1px); }
        .btn-secondary { background: var(--white); color: var(--gray-500); border: 1px solid #E2E8F0; }
        .btn-secondary:hover { background: var(--gray-100); }
        .wizard-step { display: none; }
        .wizard-step.active { display: block; }
        a.back-link { color: var(--light-blue); text-decoration: none; font-size: 0.9rem; }
        a.back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="wizard-container">
        <div class="wizard-card">
            <div class="step-indicator">
                <span class="step-dot {{ $step ?? 1 === 1 ? 'active' : 'done' }}" id="dot1"></span>
                <span class="step-dot {{ ($step ?? 1) === 2 ? 'active' : (($step ?? 1) > 2 ? 'done' : '') }}" id="dot2"></span>
            </div>

            <div class="plan-badge">
                {{ $planInfo['name'] }} — LKR {{ number_format($planInfo['price_lkr']) }}<span class="currency">/month</span>
            </div>

            <form action="{{ route('onboarding.store') }}" method="POST" id="wizardForm">
                @csrf
                <input type="hidden" name="plan" value="{{ $plan }}">

                <div class="wizard-step active" data-step="1">
                    <h1>Business information</h1>
                    <p class="subtitle">We'll create your store URL from your company name (e.g. my-store).</p>

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
                    <div class="wizard-actions">
                        <button type="button" class="btn btn-primary" id="nextStep">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <a href="{{ url('/') }}#packages" class="btn btn-secondary">Back to pricing</a>
                    </div>
                </div>

                <div class="wizard-step" data-step="2">
                    <h1>Your account</h1>
                    <p class="subtitle">You'll use this to sign in and manage your store.</p>

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
                    <div class="wizard-actions">
                        <button type="button" class="btn btn-secondary" id="prevStep"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="submit" class="btn btn-primary">Create my store</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('nextStep').addEventListener('click', function() {
            document.querySelector('.wizard-step[data-step="1"]').classList.remove('active');
            document.querySelector('.wizard-step[data-step="2"]').classList.add('active');
            document.getElementById('dot1').classList.remove('active'); document.getElementById('dot1').classList.add('done');
            document.getElementById('dot2').classList.add('active');
        });
        document.getElementById('prevStep').addEventListener('click', function() {
            document.querySelector('.wizard-step[data-step="2"]').classList.remove('active');
            document.querySelector('.wizard-step[data-step="1"]').classList.add('active');
            document.getElementById('dot2').classList.remove('active');
            document.getElementById('dot1').classList.add('active'); document.getElementById('dot1').classList.remove('done');
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
</body>
</html>
