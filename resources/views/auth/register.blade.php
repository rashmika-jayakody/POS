<x-guest-layout>
    <h2 class="auth-heading">Create an Account</h2>
    <p class="auth-subheading">Set up your free account and start selling today.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Personal Info: 2-column grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="name"><i class="fas fa-user" style="color:#4A9EFF; margin-right:6px;"></i> Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    autocomplete="name" placeholder="John Doe">
                @error('name') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="email"><i class="fas fa-envelope" style="color:#4A9EFF; margin-right:6px;"></i> Email
                    Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    placeholder="john@mystore.com">
                @error('email') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <!-- Business Information Section -->
        <div class="section-divider" style="margin-top: 24px;">
            <div class="section-divider-label">
                <i class="fas fa-store" style="color:#4A9EFF;"></i> Business Info
            </div>
        </div>

        <div class="business-card">
            <!-- Shop Name full width -->
            <div class="form-group">
                <label for="shop_name">Business / Shop Name</label>
                <input type="text" id="shop_name" name="shop_name" value="{{ old('shop_name') }}" required
                    placeholder="e.g. Green Groceries">
                @error('shop_name') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <!-- Address + Phone: 2-column grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="shop_address">Shop Address</label>
                    <input type="text" id="shop_address" name="shop_address" value="{{ old('shop_address') }}" required
                        placeholder="123 Main St">
                    @error('shop_address') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="shop_phone">Contact Number <span
                            style="font-weight:400; color:#94a3b8;">(optional)</span></label>
                    <input type="text" id="shop_phone" name="shop_phone" value="{{ old('shop_phone') }}"
                        placeholder="+1 234 567 890">
                    @error('shop_phone') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- Password Section -->
        <div class="section-divider" style="margin-top: 24px;">
            <div class="section-divider-label">
                <i class="fas fa-shield-halved" style="color:#4A9EFF;"></i> Security
            </div>
        </div>

        <!-- Password fields: 2-column grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="password"><i class="fas fa-lock" style="color:#4A9EFF; margin-right:6px;"></i>
                    Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        placeholder="Create a password">
                    <button type="button" class="pw-toggle" tabindex="-1"><i class="fas fa-eye"></i></button>
                </div>
                @error('password') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="password_confirmation"><i class="fas fa-lock" style="color:#4A9EFF; margin-right:6px;"></i>
                    Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        autocomplete="new-password" placeholder="Repeat password">
                    <button type="button" class="pw-toggle" tabindex="-1"><i class="fas fa-eye"></i></button>
                </div>
                @error('password_confirmation') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit" class="auth-btn" style="margin-top: 24px;"><i class="fas fa-user-plus"
                style="margin-right: 8px;"></i> Create My Account</button>

        <div class="auth-footer-link">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </form>
</x-guest-layout>