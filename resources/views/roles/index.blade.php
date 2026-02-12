@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-shield-alt"></i>
            Roles & Permissions
        </div>
        <div class="page-subtitle">Manage system access levels and assigned permissions.</div>
    </div>

    @if(session('success'))
        <div class="animate-in"
            style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 250px;">Role Name</th>
                        <th>Permissions Summary</th>
                        <th style="text-align: right; width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--navy-dark);">
                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--gray-500);">System Identifier: {{ $role->name }}
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                    @forelse($role->permissions as $permission)
                                        <span
                                            style="background: var(--light-blue-bg); color: var(--light-blue); padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">
                                            {{ $permission->name }}
                                        </span>
                                    @empty
                                        <span style="color: var(--gray-400); font-style: italic;">No permissions assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-secondary"
                                    style="padding: 6px 12px; font-size: 0.75rem;">
                                    <i class="fas fa-key"></i> Edit Permissions
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection