@extends('layouts.admin')

@section('title', __('Tenant Details'))

@section('content')
    <div class="page-header animate-in">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="page-title">
                    <i class="fas fa-store-alt"></i>
                    {{ $tenant->name }}
                </div>
                <div class="page-subtitle">{{ __('Tenant details, branches, and users.') }}</div>
            </div>
            <a href="{{ route('tenants.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Shops') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div
            style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="section animate-in">
        <h3 style="margin-bottom: 16px; color: var(--navy-dark);">{{ __('Details') }}</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div>
                <div style="font-size: 0.75rem; color: var(--muted); text-transform: uppercase; font-weight: 700;">{{ __('Email') }}</div>
                <div>{{ $tenant->email ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: var(--muted); text-transform: uppercase; font-weight: 700;">{{ __('Phone') }}</div>
                <div>{{ $tenant->phone ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: var(--muted); text-transform: uppercase; font-weight: 700;">{{ __('Status') }}</div>
                <span class="status-badge {{ $tenant->status == 'active' ? 'active' : 'inactive' }}">
                    <span class="status-dot"></span>
                    {{ $tenant->status == 'active' ? __('Active') : __('Suspended') }}
                </span>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: var(--muted); text-transform: uppercase; font-weight: 700;">{{ __('Store link') }}</div>
                <div style="font-size: 0.875rem;">{{ request()->getSchemeAndHttpHost() }}/app/{{ $tenant->slug }}</div>
            </div>
        </div>
        @if($tenant->address)
            <div style="margin-bottom: 24px;">
                <div style="font-size: 0.75rem; color: var(--muted); text-transform: uppercase; font-weight: 700;">{{ __('Address') }}</div>
                <div>{{ $tenant->address }}</div>
            </div>
        @endif
    </div>

    <div class="section animate-in">
        <h3 style="margin-bottom: 16px; color: var(--navy-dark);">{{ __('Branches (:count)', ['count' => $tenant->branches->count()]) }}</h3>
        @if($tenant->branches->isEmpty())
            <p style="color: var(--muted);">{{ __('No branches yet.') }}</p>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Address') }}</th>
                            <th>{{ __('Phone') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenant->branches as $branch)
                            <tr>
                                <td style="font-weight: 600;">{{ $branch->name }}</td>
                                <td>{{ $branch->address ?? '—' }}</td>
                                <td>{{ $branch->phone ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="section animate-in">
        <h3 style="margin-bottom: 16px; color: var(--navy-dark);">{{ __('Users (:count)', ['count' => $tenant->users->count()]) }}</h3>
        @if($tenant->users->isEmpty())
            <p style="color: var(--muted);">{{ __('No users yet.') }}</p>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Branch') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th style="text-align: right;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenant->users as $user)
                            <tr>
                                <td style="font-weight: 600;">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span
                                            style="background: var(--light-blue-bg); color: var(--light-blue); padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                            {{ str_replace('_', ' ', $role->name) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>{{ $user->branch?->name ?? '—' }}</td>
                                <td>
                                    <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                                        <span class="status-dot"></span>
                                        {{ $user->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary"
                                        style="padding: 6px 10px; font-size: 0.75rem;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
