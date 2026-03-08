@extends('layouts.admin')

@section('title', __('Add New Role'))

@section('content')
    <div class="page-header animate-in" style="max-width: 900px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-shield-alt"></i>
            {{ __('Add New Role') }}
        </div>
        <div class="page-subtitle">{{ __('Create a role and assign permissions. Use lowercase letters, numbers, and underscores (e.g. warehouse_staff).') }}</div>
    </div>

    <div class="section animate-in" style="max-width: 900px; margin: 0 auto;">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 24px;">
                <label for="name" style="display: block; font-weight: 600; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Role name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    placeholder="{{ __('e.g. warehouse_staff') }}"
                    style="width: 100%; max-width: 320px; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 1rem;">
                @error('name')
                    <div style="color: var(--danger, #dc2626); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: var(--navy-dark); margin-bottom: 12px;">{{ __('Permissions') }}</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px;">
                    @foreach($permissions as $permission)
                        <label style="display: flex; align-items: center; gap: 12px; padding: 15px; background: white; border: 1px solid var(--gray-200); border-radius: 12px; cursor: pointer; transition: all 0.2s ease;" class="permission-card">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}
                                style="width: 20px; height: 20px; accent-color: var(--light-blue);">
                            <div>
                                <div style="font-weight: 600; color: var(--navy-dark); font-size: 0.9rem;">{{ ucwords($permission->name) }}</div>
                                <div style="font-size: 0.75rem; color: var(--gray-500);">{{ __('Can :action', ['action' => $permission->name]) }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Create Role') }}</button>
            </div>
        </form>
    </div>

    <style>
        .permission-card:hover {
            border-color: var(--light-blue);
            background: var(--light-blue-bg);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
@endsection
