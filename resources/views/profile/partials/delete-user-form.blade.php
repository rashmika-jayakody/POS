<div class="profile-section">
    <div class="settings-card-title" style="margin-bottom: 6px;"><i class="fas fa-exclamation-triangle" style="color: var(--accent-coral);"></i> {{ __('Delete Account') }}</div>
    <div class="settings-card-subtitle" style="margin-bottom: 20px;">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</div>

    <button type="button" class="btn" id="profileDeleteAccountBtn" style="background: rgba(255, 107, 130, 0.12); color: var(--accent-coral); border: 1px solid rgba(255, 107, 130, 0.3);">
        <i class="fas fa-trash-alt"></i> {{ __('Delete Account') }}
    </button>
</div>

{{-- Vanilla JS modal (admin layout has no Alpine) --}}
<div id="confirm-user-deletion-modal" class="profile-delete-modal" style="display: {{ $errors->userDeletion->isNotEmpty() ? 'flex' : 'none' }};">
    <div class="profile-delete-modal-backdrop" id="profileDeleteModalBackdrop"></div>
    <div class="profile-delete-modal-box">
        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Are you sure you want to delete your account?') }}</h2>
            <p style="font-size: 0.9rem; color: var(--gray-600); margin-bottom: 20px;">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>

            <div style="margin-bottom: 20px;">
                <label for="profile_delete_password" style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Password') }}</label>
                <input type="password" id="profile_delete_password" name="password" placeholder="{{ __('Password') }}"
                    style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                @if(optional($errors->userDeletion)->get('password'))
                    @foreach($errors->userDeletion->get('password') as $msg)
                        <span style="color: var(--danger); font-size: 0.8rem;">{{ $msg }}</span>
                    @endforeach
                @endif
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" id="profileDeleteModalCancel">{{ __('Cancel') }}</button>
                <button type="submit" class="btn" style="background: var(--accent-coral); color: var(--white); border: none;"><i class="fas fa-trash-alt"></i> {{ __('Delete Account') }}</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.profile-delete-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.profile-delete-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(10, 26, 61, 0.5);
}
.profile-delete-modal-box {
    position: relative;
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    padding: 28px;
    max-width: 440px;
    width: 100%;
    border: 1px solid var(--gray-200);
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    var btn = document.getElementById('profileDeleteAccountBtn');
    var modal = document.getElementById('confirm-user-deletion-modal');
    var backdrop = document.getElementById('profileDeleteModalBackdrop');
    var cancelBtn = document.getElementById('profileDeleteModalCancel');

    function openModal() {
        if (modal) modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    if (btn) btn.addEventListener('click', openModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') closeModal();
    });
})();
</script>
@endpush
