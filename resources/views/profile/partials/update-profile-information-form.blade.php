<div class="profile-section">
    <div class="settings-card-title" style="margin-bottom: 6px;"><i class="fas fa-user"></i> {{ __('Profile Information') }}</div>
    <div class="settings-card-subtitle" style="margin-bottom: 20px;">{{ __('Update your account\'s profile information and email address.') }}</div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
            @error('name') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="email" style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
            @error('email') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p style="font-size: 0.85rem; color: var(--gray-700); margin-top: 10px;">
                    {{ __('Your email address is unverified.') }}
                    <button type="submit" form="send-verification" style="background: none; border: none; padding: 0; color: var(--light-blue); text-decoration: underline; cursor: pointer; font-size: inherit;">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p style="margin-top: 8px; font-size: 0.85rem; font-weight: 600; color: var(--success);">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif
            @endif
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; border-top: 1px solid var(--gray-100); padding-top: 20px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> {{ __('Save') }}</button>
            @if (session('status') === 'profile-updated')
                <span style="font-size: 0.85rem; color: var(--success); font-weight: 600;"><i class="fas fa-check-circle"></i> {{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</div>
