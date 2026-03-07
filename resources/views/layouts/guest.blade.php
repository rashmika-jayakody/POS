<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'POS System') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Vite Assets (for form component styles) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
        }

        /* ─── Left Branding Pane ─── */
        .auth-branding {
            display: none;
            position: relative;
            width: 50%;
            background: linear-gradient(135deg, #0A1A3D 0%, #1A2B4D 50%, #0f3460 100%);
            overflow: hidden;
            align-items: center;
            justify-content: center;
        }

        @media (min-width: 1024px) {
            .auth-branding {
                display: flex;
            }
        }

        .auth-branding::before {
            content: '';
            position: absolute;
            top: -120px;
            left: -120px;
            width: 480px;
            height: 480px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 50%;
        }

        .auth-branding::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 380px;
            height: 380px;
            background: rgba(0, 201, 183, 0.2);
            border-radius: 50%;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 48px;
        }

        .brand-icon {
            width: 96px;
            height: 96px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            backdrop-filter: blur(8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .brand-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
            line-height: 1.15;
        }

        .brand-subtitle {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.7;
            max-width: 380px;
        }

        .brand-features {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            text-align: left;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(8px);
        }

        .brand-feature i {
            color: #00C9B7;
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .brand-feature span {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* ─── Right Form Pane ─── */
        .auth-form-pane {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0A1A3D 0%, #1A2B4D 60%, #0f3460 100%);
            padding: 40px 24px;
            position: relative;
            overflow: hidden;
        }

        .auth-form-pane::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(74, 158, 255, 0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        .auth-form-pane::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(0, 201, 183, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .auth-card {
            width: 100%;
            max-width: 100%;
            background: white;
            border-radius: 20px;
            padding: 48px 48px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35), 0 8px 24px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        /* Narrow inner wrapper – used by login page to limit width */
        .auth-narrow {
            max-width: 460px;
            margin: 0 auto;
        }

        /* Mobile logo */
        .mobile-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 28px;
        }

        .mobile-logo-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #0A1A3D, #4A9EFF);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(10, 26, 61, 0.35);
        }

        .mobile-logo-icon i {
            color: white;
            font-size: 1.5rem;
        }

        @media (min-width: 1024px) {
            .mobile-logo {
                display: none;
            }
        }

        /* ─── Auth Form Shared Styles ─── */
        .auth-heading {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0A1A3D;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .auth-subheading {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="url"],
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: #0f172a;
            background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #4A9EFF;
            background: white;
            box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.15);
        }

        .form-group input::placeholder {
            color: #94a3b8;
        }

        .auth-btn {
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, #4A9EFF 0%, #00C9B7 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 12px rgba(10, 26, 61, 0.3);
            margin-top: 8px;
        }

        .auth-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(10, 26, 61, 0.4);
        }

        .auth-btn:active {
            transform: translateY(0);
        }

        .auth-footer-link {
            text-align: center;
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 24px;
        }

        .auth-footer-link a {
            color: #4A9EFF;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }

        .auth-footer-link a:hover {
            color: #1A2B4D;
            text-decoration: underline;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #4A9EFF;
            cursor: pointer;
        }

        .form-check label {
            font-size: 0.875rem;
            color: #374151;
            font-weight: 500;
            margin: 0;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.875rem;
            color: #4A9EFF;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }

        .forgot-link:hover {
            color: #1A2B4D;
        }

        .section-divider {
            border-top: 1.5px solid #e2e8f0;
            margin: 28px 0 24px;
            position: relative;
        }

        .section-divider-label {
            position: absolute;
            top: -11px;
            left: 0;
            background: white;
            padding-right: 12px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #4338ca;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .business-card {
            background: #EFF6FF;
            border: 1.5px solid #BFDBFE;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 8px;
        }

        .business-card .form-group {
            margin-bottom: 14px;
        }

        .business-card .form-group:last-child {
            margin-bottom: 0;
        }

        .business-card .form-group input {
            background: white;
        }

        .error-text {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #4338ca;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            margin-bottom: 24px;
            transition: color 0.15s;
        }

        .back-link:hover {
            color: #1A2B4D;
        }

        /* ─── Password Toggle ─── */
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 46px !important;
        }

        .pw-toggle {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            font-size: 1rem;
            padding: 0;
            line-height: 1;
            transition: color 0.15s;
        }

        .pw-toggle:hover {
            color: #4A9EFF;
        }
    </style>
</head>

<body>
    <!-- Left Branding Pane -->
    <div class="auth-branding">
        <div class="brand-content">
            <div class="brand-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="brand-title">Empower Your<br>Business</div>
            <div class="brand-subtitle">Manage sales, inventory, and growth with our all-in-one Point of Sale system.
            </div>
            <div class="brand-features">
                <div class="brand-feature">
                    <i class="fas fa-chart-line"></i>
                    <span>Real-time sales analytics</span>
                </div>
                <div class="brand-feature">
                    <i class="fas fa-boxes"></i>
                    <span>Smart inventory tracking</span>
                </div>
                <div class="brand-feature">
                    <i class="fas fa-receipt"></i>
                    <span>Professional receipts &amp; invoices</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Form Pane -->
    <div class="auth-form-pane">
        <div class="auth-card">
            <div class="mobile-logo">
                <div class="mobile-logo-icon">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>
            {{ $slot }}
        </div>
    </div>
</body>

<script>
    document.querySelectorAll('.pw-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = this.previousElementSibling;
            var icon = this.querySelector('i');
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
</script>

</html>