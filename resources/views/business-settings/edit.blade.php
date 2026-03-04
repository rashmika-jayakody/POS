@extends('layouts.admin')

@section('title', 'Business Settings')

@section('content')
    <div class="page-header animate-in" style="max-width: 900px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-cog"></i>
            Business Settings
        </div>
        <div class="page-subtitle">Customize your business name, logo, currency, taxes, and brand colors.</div>
    </div>

    @if (session('success'))
        <div class="animate-in" style="max-width: 900px; margin: 0 auto 20px auto; padding: 14px 18px; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: var(--radius-md); color: var(--success); font-weight: 600;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="section animate-in" style="max-width: 900px; margin: 0 auto;">
        <form action="{{ route('business-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="section-title"><i class="fas fa-store"></i> Branding</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $settings->business_name) }}" placeholder="e.g. My Store"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('business_name') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Logo</label>
                    @if ($settings->logo_path)
                        <div style="margin-bottom: 10px;">
                            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" style="max-height: 100px; max-width: 280px; object-fit: contain;">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                        style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                    <span style="font-size: 0.8rem; color: var(--gray-500);">PNG, JPG, GIF or WebP, max 2MB. Leave empty to keep current.</span>
                    @error('logo') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="section-title"><i class="fas fa-palette"></i> Brand Colors</div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Primary Color</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $settings->primary_color ?? '#4A9EFF') }}"
                            style="width: 48px; height: 40px; border: 1px solid var(--gray-300); border-radius: 8px; cursor: pointer;">
                        <input type="text" value="{{ old('primary_color', $settings->primary_color ?? '#4A9EFF') }}" id="primary_color_text" maxlength="20"
                            style="flex: 1; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;"
                            onchange="document.getElementById('primary_color').value = this.value">
                    </div>
                    @error('primary_color') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Secondary Color</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', $settings->secondary_color ?? '#0A1A3D') }}"
                            style="width: 48px; height: 40px; border: 1px solid var(--gray-300); border-radius: 8px; cursor: pointer;">
                        <input type="text" value="{{ old('secondary_color', $settings->secondary_color ?? '#0A1A3D') }}" id="secondary_color_text" maxlength="20"
                            style="flex: 1; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;"
                            onchange="document.getElementById('secondary_color').value = this.value">
                    </div>
                    @error('secondary_color') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Accent Color</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="accent_color" id="accent_color" value="{{ old('accent_color', $settings->accent_color ?? '#00C9B7') }}"
                            style="width: 48px; height: 40px; border: 1px solid var(--gray-300); border-radius: 8px; cursor: pointer;">
                        <input type="text" value="{{ old('accent_color', $settings->accent_color ?? '#00C9B7') }}" id="accent_color_text" maxlength="20"
                            style="flex: 1; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;"
                            onchange="document.getElementById('accent_color').value = this.value">
                    </div>
                    @error('accent_color') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>
            <p style="font-size: 0.85rem; color: var(--gray-500); margin-bottom: 24px;">Colors are used in the dashboard sidebar and header. Leave default if you prefer.</p>

            <div class="section-title"><i class="fas fa-coins"></i> Currency & Tax</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Currency Code</label>
                    <select name="currency_code" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        @foreach (['LKR' => 'LKR - Sri Lankan Rupee', 'USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'GBP' => 'GBP - British Pound', 'INR' => 'INR - Indian Rupee', 'AUD' => 'AUD - Australian Dollar'] as $code => $label)
                            <option value="{{ $code }}" {{ old('currency_code', $settings->currency_code ?? 'LKR') == $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('currency_code') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings->currency_symbol ?? 'Rs') }}" placeholder="Rs, $, €"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('currency_symbol') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Tax Rate (%)</label>
                    <input type="number" name="tax_rate" value="{{ old('tax_rate', $settings->tax_rate ?? 0) }}" min="0" max="100" step="0.01" placeholder="0"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('tax_rate') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Tax Label (e.g. VAT, GST)</label>
                    <input type="text" name="tax_label" value="{{ old('tax_label', $settings->tax_label) }}" placeholder="VAT"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('tax_label') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="section-title"><i class="fas fa-address-card"></i> Contact & Receipt</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Address</label>
                    <input type="text" name="address" value="{{ old('address', $settings->address) }}" placeholder="Business address"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('address') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}" placeholder="+94 11 234 5678"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('phone') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Email</label>
                    <input type="email" name="email" value="{{ old('email', $settings->email) }}" placeholder="hello@mystore.com"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('email') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Website</label>
                    <input type="url" name="website" value="{{ old('website', $settings->website) }}" placeholder="https://mystore.com"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('website') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Receipt Header (shown on printed receipts)</label>
                    <input type="text" name="receipt_header" value="{{ old('receipt_header', $settings->receipt_header) }}" placeholder="Thank you for shopping with us!"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('receipt_header') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Receipt Footer</label>
                    <input type="text" name="receipt_footer" value="{{ old('receipt_footer', $settings->receipt_footer) }}" placeholder="Visit again!"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('receipt_footer') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('input[type="color"]').forEach(function (colorInput) {
            var textId = colorInput.id + '_text';
            var textEl = document.getElementById(textId);
            if (textEl) {
                colorInput.addEventListener('input', function () { textEl.value = this.value; });
                textEl.addEventListener('input', function () { colorInput.value = this.value; });
            }
        });
    </script>
    @endpush
@endsection
