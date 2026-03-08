@extends('layouts.admin')

@section('title', __('Profile'))

@push('styles')
<style>
.profile-section .settings-card-title { font-size: 1.05rem; font-weight: 700; color: var(--navy-dark); display: flex; align-items: center; gap: 8px; }
.profile-section .settings-card-title i { color: var(--light-blue); }
.profile-section .settings-card-subtitle { font-size: 0.9rem; color: var(--gray-500); line-height: 1.4; }
</style>
@endpush

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-user-cog"></i>
            {{ __('Profile') }}
        </div>
        <div class="page-subtitle">{{ __('Update your account profile information and password.') }}</div>
    </div>

    @hasrole('business_owner')
    @if(auth()->user()->tenant)
    <div class="section animate-in" style="max-width: 42rem;">
        @include('profile.partials.subscription-plan')
    </div>
    @endif
@endhasrole

    <div class="section animate-in" style="max-width: 42rem;">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="section animate-in" style="max-width: 42rem;">
        @include('profile.partials.update-password-form')
    </div>

    <div class="section animate-in" style="max-width: 42rem;">
        @include('profile.partials.delete-user-form')
    </div>
@endsection
