<x-guest-layout>
    <div class="auth-narrow">
        <h2 class="auth-heading">Welcome back</h2>
        <p class="auth-subheading">Sign in to your account to continue.</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope" style="color:#4A9EFF; margin-right:6px;"></i> Email
                    Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username" placeholder="hello@yourstore.com">
                @error('email') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock" style="color:#4A9EFF; margin-right:6px;"></i>
                    Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••">
                    <button type="button" class="pw-toggle" tabindex="-1"><i class="fas fa-eye"></i></button>
                </div>
                @error('password') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-check">
                    <input type="checkbox" id="remember_me" name="remember">
                    <label for="remember_me">Remember me</label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="auth-btn"><i class="fas fa-arrow-right-to-bracket"
                    style="margin-right: 8px;"></i>
                Sign In</button>

            <div class="auth-footer-link">
                Don't have an account? <a href="{{ route('onboarding.index', ['plan' => 'professional']) }}">Create one free</a>
            </div>

            <div class="auth-footer-link" style="margin-top: 12px;">
                <a href="{{ url('/') }}" style="color: #94a3b8; font-weight: 500;">
                    <i class="fas fa-arrow-left" style="font-size: 0.75rem; margin-right: 4px;"></i> Back to Home
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>