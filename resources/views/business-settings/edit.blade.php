@extends('layouts.admin')

@section('title', __('Business Settings'))

@section('content')
    <div class="page-header animate-in" style="max-width: 1200px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-cog"></i>
            {{ __('Business Settings') }}
        </div>
        <div class="page-subtitle">{{ __('Customize your business name, logo, currency, taxes, and brand colors.') }}</div>
    </div>

    @if (session('success'))
        <div class="animate-in"
            style="max-width: 1200px; margin: 0 auto 20px auto; padding: 14px 18px; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: var(--radius-md); color: var(--success); font-weight: 600;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @push('styles')
        <style>
            .settings-container {
                max-width: 1200px;
                margin: 0 auto;
            }

            .settings-layout {
                display: flex;
                flex-direction: column;
                gap: 28px;
            }

            /* Modern Tabs - Forced Single Row */
            .settings-tabs {
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 12px;
                padding-bottom: 12px;
                -webkit-overflow-scrolling: touch;
                border-bottom: 1px solid var(--gray-200);
            }

            .tab-btn {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 14px 20px;
                background: var(--white);
                border: 1px solid var(--gray-200);
                border-radius: var(--radius-md);
                font-weight: 600;
                color: var(--gray-600);
                cursor: pointer;
                transition: all 0.3s ease;
                white-space: nowrap;
                box-shadow: 0 2px 4px rgba(0,0,0,0.02);
                text-align: left;
            }

            .tab-btn i {
                font-size: 1.1rem;
                width: 20px;
                text-align: center;
                color: var(--gray-400);
                transition: color 0.3s ease;
            }

            .tab-btn:hover {
                border-color: var(--light-blue-light);
                background: var(--light-blue-bg);
                color: var(--navy-dark);
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.04);
            }

            .tab-btn:hover i {
                color: var(--light-blue);
            }

            .tab-btn.active {
                background: var(--light-blue);
                color: var(--white);
                border-color: var(--light-blue);
                box-shadow: 0 4px 12px rgba(74, 158, 255, 0.3);
            }

            .tab-btn.active i {
                color: var(--white);
            }

            /* Modern Content Cards */
            .settings-content-wrapper {
                flex: 1;
                min-width: 0;
            }

            .tab-content {
                display: none;
                animation: slideUpFade 0.4s ease forwards;
            }

            .tab-content.active {
                display: block;
            }

            .settings-card {
                background: var(--white);
                border-radius: var(--radius-lg);
                border: 1px solid var(--gray-200);
                box-shadow: var(--shadow-sm);
                padding: 32px;
                margin-bottom: 24px;
            }

            .settings-card-header {
                margin-bottom: 28px;
                padding-bottom: 16px;
                border-bottom: 1px solid var(--gray-100);
            }

            .settings-card-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--navy-dark);
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .settings-card-title i {
                color: var(--light-blue);
                background: var(--light-blue-bg);
                padding: 8px;
                border-radius: var(--radius-sm);
                font-size: 1.1rem;
            }

            .settings-card-subtitle {
                font-size: 0.85rem;
                color: var(--gray-500);
                margin-top: 6px;
                margin-left: 44px;
            }

            /* Modern Input Styling */
            .modern-input-group {
                margin-bottom: 24px;
            }

            .modern-label {
                display: block;
                font-weight: 600;
                color: var(--navy-medium);
                margin-bottom: 8px;
                font-size: 0.95rem;
            }

            .modern-input {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid var(--gray-300);
                border-radius: var(--radius-sm);
                font-family: inherit;
                font-size: 0.95rem;
                color: var(--navy-dark);
                background-color: var(--white);
                transition: all 0.2s ease;
            }

            .modern-input:focus {
                outline: none;
                border-color: var(--light-blue);
                box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.15);
            }

            .modern-input::placeholder {
                color: var(--gray-400);
            }

            .modern-color-picker {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 8px;
                border: 1px solid var(--gray-300);
                border-radius: var(--radius-sm);
                background: var(--white);
            }

            .modern-color-picker input[type="color"] {
                -webkit-appearance: none;
                border: none;
                width: 36px;
                height: 36px;
                border-radius: 6px;
                cursor: pointer;
                padding: 0;
                overflow: hidden;
                background: transparent;
            }

            .modern-color-picker input[type="color"]::-webkit-color-swatch-wrapper {
                padding: 0;
            }

            .modern-color-picker input[type="color"]::-webkit-color-swatch {
                border: none;
                border-radius: 6px;
                box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1);
            }

            .modern-color-picker input[type="text"] {
                border: none;
                flex: 1;
                font-family: monospace;
                font-size: 1rem;
                color: var(--navy-dark);
                outline: none;
                background: transparent;
                font-weight: 600;
            }

            .modern-file-upload {
                border: 2px dashed var(--gray-300);
                border-radius: var(--radius-md);
                padding: 24px;
                text-align: center;
                background: var(--gray-light);
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .modern-file-upload:hover {
                border-color: var(--light-blue);
                background: var(--light-blue-bg);
            }

            @keyframes slideUpFade {
                from {
                    opacity: 0;
                    transform: translateY(15px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush

    <div class="settings-container animate-in">
        <form action="{{ route('business-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="settings-layout">
                <!-- Sidebar Tab Navigation -->
                <div class="settings-tabs">
                    <button type="button" class="tab-btn active" data-tab="general">
                        <i class="fas fa-store"></i> <span>{{ __('General Info') }}</span>
                    </button>
                    <button type="button" class="tab-btn" data-tab="branding">
                        <i class="fas fa-palette"></i> <span>{{ __('Branding & Colors') }}</span>
                    </button>
                    <button type="button" class="tab-btn" data-tab="financial">
                        <i class="fas fa-coins"></i> <span>{{ __('Currency & Tax') }}</span>
                    </button>
                    <button type="button" class="tab-btn" data-tab="contact">
                        <i class="fas fa-address-card"></i> <span>{{ __('Contact & Receipt') }}</span>
                    </button>
                </div>

                <!-- Main Content Area -->
                <div class="settings-content-wrapper">

                    <!-- Tab Content: General Info -->
                    <div class="tab-content active" id="tab-general">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-title"><i class="fas fa-store"></i> {{ __('General Information') }}</div>
                                <div class="settings-card-subtitle">{{ __('Basic details about your business used across the system.') }}</div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Business Name') }}</label>
                                    <input type="text" class="modern-input" name="business_name" value="{{ old('business_name', $settings->business_name) }}" placeholder="{{ __('e.g. My Store') }}" required>
                                    @error('business_name') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content: Branding & Colors -->
                    <div class="tab-content" id="tab-branding">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-title"><i class="fas fa-image"></i> {{ __('Brand Logo') }}</div>
                                <div class="settings-card-subtitle">{{ __('Upload your company logo. It will appear on the dashboard and printed receipts.') }}</div>
                            </div>

                            <div class="modern-input-group" style="max-width: 500px;">
                                <div id="logo-preview-container" style="margin-bottom: 16px; padding: 16px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); background: var(--gray-light); display: none; text-align: center;">
                                    <div style="font-weight: 600; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Logo Preview') }}</div>
                                    <img id="logo-preview" src="" alt="Logo Preview" style="max-height: 120px; max-width: 100%; object-fit: contain; border-radius: 4px;">
                                </div>
                                @if ($settings->logo_path)
                                    <div id="current-logo" style="margin-bottom: 16px; padding: 16px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); background: var(--gray-light); display: inline-block;">
                                        <div style="font-weight: 600; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Current Logo') }}</div>
                                        <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Current Logo" style="max-height: 120px; max-width: 100%; object-fit: contain;">
                                    </div>
                                @endif
                                <div class="modern-file-upload" onclick="document.getElementById('logo-upload').click()">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: var(--light-blue); margin-bottom: 12px;"></i>
                                    <div style="font-weight: 600; color: var(--navy-dark); margin-bottom: 4px;">{{ __('Click to upload a new logo') }}</div>
                                    <div style="font-size: 0.85rem; color: var(--gray-500);">{{ __('PNG, JPG, GIF or WebP (max. 2MB)') }}</div>
                                    <input type="file" id="logo-upload" name="logo" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="display: none;">
                                </div>
                                @error('logo') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-title"><i class="fas fa-palette"></i> {{ __('Brand Colors') }}</div>
                                <div class="settings-card-subtitle">{{ __('Personalize your dashboard\'s theme. These colors update the sidebar and active links.') }}</div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Primary Color') }}</label>
                                    <div class="modern-color-picker">
                                        <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $settings->primary_color ?? '#4A9EFF') }}">
                                        <input type="text" value="{{ old('primary_color', $settings->primary_color ?? '#4A9EFF') }}" id="primary_color_text" maxlength="20" onchange="document.getElementById('primary_color').value = this.value">
                                    </div>
                                    @error('primary_color') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>

                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Secondary Color') }}</label>
                                    <div class="modern-color-picker">
                                        <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', $settings->secondary_color ?? '#0A1A3D') }}">
                                        <input type="text" value="{{ old('secondary_color', $settings->secondary_color ?? '#0A1A3D') }}" id="secondary_color_text" maxlength="20" onchange="document.getElementById('secondary_color').value = this.value">
                                    </div>
                                    @error('secondary_color') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>

                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Accent Color') }}</label>
                                    <div class="modern-color-picker">
                                        <input type="color" name="accent_color" id="accent_color" value="{{ old('accent_color', $settings->accent_color ?? '#00C9B7') }}">
                                        <input type="text" value="{{ old('accent_color', $settings->accent_color ?? '#00C9B7') }}" id="accent_color_text" maxlength="20" onchange="document.getElementById('accent_color').value = this.value">
                                    </div>
                                    @error('accent_color') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content: Currency & Tax -->
                    <div class="tab-content" id="tab-financial">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-title"><i class="fas fa-coins"></i> {{ __('Currency Settings') }}</div>
                                <div class="settings-card-subtitle">{{ __('Set your default currency for transactions and reports.') }}</div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Currency Code') }}</label>
                                    <select name="currency_code" class="modern-input" style="appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23131313%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 16px top 50%; background-size: 10px auto;">
                                        @foreach (['LKR' => 'LKR - Sri Lankan Rupee', 'USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'GBP' => 'GBP - British Pound', 'INR' => 'INR - Indian Rupee', 'AUD' => 'AUD - Australian Dollar'] as $code => $label)
                                            <option value="{{ $code }}" {{ old('currency_code', $settings->currency_code ?? 'LKR') == $code ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('currency_code') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Currency Symbol') }}</label>
                                    <input type="text" class="modern-input" name="currency_symbol" value="{{ old('currency_symbol', $settings->currency_symbol ?? 'Rs') }}" placeholder="Rs, $, €">
                                    @error('currency_symbol') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-title"><i class="fas fa-percent"></i> {{ __('Tax Configuration') }}</div>
                                <div class="settings-card-subtitle">{{ __('Configure default tax rates applied to sales.') }}</div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Default Tax Rate (%)') }}</label>
                                    <input type="number" class="modern-input" name="tax_rate" value="{{ old('tax_rate', $settings->tax_rate ?? 0) }}" min="0" max="100" step="0.01" placeholder="0">
                                    @error('tax_rate') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Tax Label (e.g. VAT, GST)') }}</label>
                                    <input type="text" class="modern-input" name="tax_label" value="{{ old('tax_label', $settings->tax_label) }}" placeholder="VAT">
                                    @error('tax_label') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content: Contact & Receipt -->
                    <div class="tab-content" id="tab-contact">
                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-title"><i class="fas fa-address-card"></i> {{ __('Contact Information') }}</div>
                                <div class="settings-card-subtitle">{{ __('These details appear on your printed receipts and customer communications.') }}</div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Primary Address') }}</label>
                                    <input type="text" class="modern-input" name="address" value="{{ old('address', $settings->address) }}" placeholder="{{ __('e.g. 123 Main St') }}">
                                    @error('address') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Phone Number') }}</label>
                                    <input type="text" class="modern-input" name="phone" value="{{ old('phone', $settings->phone) }}" placeholder="+94 11 234 5678">
                                    @error('phone') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Email Address') }}</label>
                                    <input type="email" class="modern-input" name="email" value="{{ old('email', $settings->email) }}" placeholder="hello@mystore.com">
                                    @error('email') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                                <div class="modern-input-group">
                                    <label class="modern-label">{{ __('Website') }}</label>
                                    <input type="url" class="modern-input" name="website" value="{{ old('website', $settings->website) }}" placeholder="https://mystore.com">
                                    @error('website') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="settings-card">
                            <div class="settings-card-header">
                                <div class="settings-card-title"><i class="fas fa-receipt"></i> {{ __('Custom Receipt Messages') }}</div>
                                <div class="settings-card-subtitle">{{ __('Add personalized greetings or messages to the top and bottom of your printed receipts.') }}</div>
                            </div>

                            <div class="modern-input-group">
                                <label class="modern-label">{{ __('Receipt Header Message') }}</label>
                                <input type="text" class="modern-input" name="receipt_header" value="{{ old('receipt_header', $settings->receipt_header) }}" placeholder="{{ __('e.g. Thank you for shopping with us!') }}">
                                @error('receipt_header') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                            </div>
                            <div class="modern-input-group" style="margin-bottom: 0;">
                                <label class="modern-label">{{ __('Receipt Footer Message') }}</label>
                                <input type="text" class="modern-input" name="receipt_footer" value="{{ old('receipt_footer', $settings->receipt_footer) }}" placeholder="{{ __('e.g. Visit again soon!') }}">
                                @error('receipt_footer') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); padding: 20px 32px; display: flex; gap: 16px; justify-content: flex-end; align-items: center; margin-bottom: 40px; position: sticky; bottom: 0; z-index: 10;">
                        <span style="color: var(--gray-500); font-size: 0.85rem; margin-right: auto;"><i class="fas fa-info-circle"></i> {{ __('Unsaved changes will be lost') }}</span>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="padding: 12px 24px;">{{ __('Discard') }}</a>
                        <button type="submit" id="saveSettingsBtn" class="btn btn-primary" style="padding: 12px 28px; box-shadow: 0 4px 12px rgba(74, 158, 255, 0.4);"><i class="fas fa-check"></i> {{ __('Save Changes') }}</button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            window.__bsTranslations = @json([
                'file_size_2mb' => __('File size must be less than 2MB'),
                'valid_image_type' => __('Please select a valid image file (PNG, JPG, GIF, or WebP)')
            ]);
            // Tab switching logic
            document.querySelectorAll('.tab-btn').forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class from all buttons and tabs
                    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));

                    // Add active class to clicked button
                    button.classList.add('active');

                    // Show corresponding tab content
                    const tabId = 'tab-' + button.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Color picker synchronization
            document.querySelectorAll('input[type="color"]').forEach(function (colorInput) {
                var textId = colorInput.id + '_text';
                var textEl = document.getElementById(textId);
                if (textEl) {
                    colorInput.addEventListener('input', function () { 
                        textEl.value = this.value; 
                    });
                    textEl.addEventListener('input', function () { 
                        // Validate hex color format
                        var value = this.value.trim();
                        if (/^#[0-9A-F]{6}$/i.test(value)) {
                            colorInput.value = value;
                        }
                    });
                    textEl.addEventListener('blur', function () {
                        // Sync on blur to ensure color input is updated
                        var value = this.value.trim();
                        if (/^#[0-9A-F]{6}$/i.test(value)) {
                            colorInput.value = value;
                        } else {
                            // If invalid, restore from color input
                            this.value = colorInput.value;
                        }
                    });
                }
            });
            
            // Ensure color values are synced before form submission
            const form = document.querySelector('form[action="{{ route("business-settings.update") }}"]');
            const saveBtn = document.getElementById('saveSettingsBtn');
            
            if (form) {
                // Sync color inputs before form submission
                form.addEventListener('submit', function(e) {
                    // Sync all color text inputs to color inputs before submission
                    document.querySelectorAll('input[type="color"]').forEach(function (colorInput) {
                        var textId = colorInput.id + '_text';
                        var textEl = document.getElementById(textId);
                        if (textEl) {
                            var value = textEl.value.trim();
                            // Ensure value starts with #
                            if (!value.startsWith('#')) {
                                value = '#' + value;
                            }
                            // Validate and sync hex color
                            if (/^#[0-9A-F]{6}$/i.test(value)) {
                                colorInput.value = value.toUpperCase();
                                textEl.value = value.toUpperCase();
                            } else {
                                // If invalid, keep current color input value
                                textEl.value = colorInput.value;
                            }
                        }
                    });
                    // Form will submit normally after sync - don't prevent default
                });
                
                // Also sync on text input blur to ensure values are always in sync
                document.querySelectorAll('input[id$="_text"]').forEach(function(textInput) {
                    textInput.addEventListener('blur', function() {
                        var colorId = this.id.replace('_text', '');
                        var colorInput = document.getElementById(colorId);
                        if (colorInput) {
                            var value = this.value.trim();
                            if (!value.startsWith('#')) {
                                value = '#' + value;
                            }
                            if (/^#[0-9A-F]{6}$/i.test(value)) {
                                colorInput.value = value.toUpperCase();
                                this.value = value.toUpperCase();
                            } else {
                                this.value = colorInput.value;
                            }
                        }
                    });
                });
                
                // Ensure save button is clickable and not disabled
                if (saveBtn) {
                    saveBtn.addEventListener('click', function(e) {
                        // Ensure button is not disabled
                        if (this.disabled) {
                            e.preventDefault();
                            return false;
                        }
                        // Trigger form submission if not already submitting
                        if (!form.checkValidity()) {
                            form.reportValidity();
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            }
            
            // Logo preview functionality
            const logoUpload = document.getElementById('logo-upload');
            const logoPreview = document.getElementById('logo-preview');
            const logoPreviewContainer = document.getElementById('logo-preview-container');
            const currentLogo = document.getElementById('current-logo');
            
            if (logoUpload) {
                logoUpload.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert(window.__bsTranslations.file_size_2mb || 'File size must be less than 2MB');
                            this.value = '';
                            return;
                        }
                        
                        // Validate file type
                        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                        if (!validTypes.includes(file.type)) {
                            alert(window.__bsTranslations.valid_image_type || 'Please select a valid image file (PNG, JPG, GIF, or WebP)');
                            this.value = '';
                            return;
                        }
                        
                        // Show preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            logoPreview.src = e.target.result;
                            logoPreviewContainer.style.display = 'block';
                            if (currentLogo) {
                                currentLogo.style.display = 'none';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        </script>
    @endpush
@endsection