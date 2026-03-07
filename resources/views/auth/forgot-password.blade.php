<x-guest-layout>
    <a href="{{ route('login') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to login
    </a>

    <h2 class="auth-heading">Reset Password</h2>
    <p class="auth-subheading">Enter your email and we'll send you a password reset link.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email"><i class="fas fa-envelope" style="color:#4338ca; margin-right:6px;"></i> Email
                Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="john@mystore.com">
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="auth-btn"><i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Send Reset
            Link</button>
    </form>
</x-guest-layout>