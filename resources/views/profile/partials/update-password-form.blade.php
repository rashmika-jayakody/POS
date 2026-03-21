<div class="profile-section">
    <div class="settings-card-title" style="margin-bottom: 6px;"><i class="fas fa-lock"></i> {{ __('Update Password') }}</div>
    <div class="settings-card-subtitle" style="margin-bottom: 20px;">{{ __('Ensure your account is using a long, random password to stay secure.') }}</div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div style="margin-bottom: 20px;">
            <label for="update_password_current_password" style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Current Password') }}</label>
            <input type="password" id="update_password_current_password" name="current_password" autocomplete="current-password"
                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
            @if(optional($errors->updatePassword)->get('current_password'))
                @foreach($errors->updatePassword->get('current_password') as $msg)
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $msg }}</span>
                @endforeach
            @endif
        </div>

        <div style="margin-bottom: 20px;">
            <label for="update_password_password" style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('New Password') }}</label>
            <input type="password" id="update_password_password" name="password" autocomplete="new-password"
                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
            @if(optional($errors->updatePassword)->get('password'))
                @foreach($errors->updatePassword->get('password') as $msg)
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $msg }}</span>
                @endforeach
            @endif
        </div>

        <div style="margin-bottom: 20px;">
            <label for="update_password_password_confirmation" style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Confirm Password') }}</label>
            <input type="password" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password"
                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
            @if(optional($errors->updatePassword)->get('password_confirmation'))
                @foreach($errors->updatePassword->get('password_confirmation') as $msg)
                    <span style="color: var(--danger); font-size: 0.8rem;">{{ $msg }}</span>
                @endforeach
            @endif
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; border-top: 1px solid var(--gray-100); padding-top: 20px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> {{ __('Save') }}</button>
            @if (session('status') === 'password-updated')
                <span style="font-size: 0.85rem; color: var(--success); font-weight: 600;"><i class="fas fa-check-circle"></i> {{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</div>
