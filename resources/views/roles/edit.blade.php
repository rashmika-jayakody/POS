@extends('layouts.admin')

@section('title', 'Edit Role Permissions')

@section('content')
    <div class="page-header animate-in" style="max-width: 900px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-key"></i>
            Permissions for: {{ ucwords(str_replace('_', ' ', $role->name)) }}
        </div>
        <div class="page-subtitle">Configure what users with this role can see and do.</div>
    </div>

    <div class="section animate-in" style="max-width: 900px; margin: 0 auto;">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px; margin-bottom: 30px;">
                @foreach($permissions as $permission)
                    <label style="display: flex; align-items: center; gap: 12px; padding: 15px; background: white; border: 1px solid var(--gray-200); border-radius: 12px; cursor: pointer; transition: all 0.2s ease;" class="permission-card">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                            {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                            style="width: 20px; height: 20px; accent-color: var(--light-blue);">
                        <div>
                            <div style="font-weight: 600; color: var(--navy-dark); font-size: 0.9rem;">{{ ucwords($permission->name) }}</div>
                            <div style="font-size: 0.75rem; color: var(--gray-500);">Can {{ $permission->name }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Permissions</button>
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
