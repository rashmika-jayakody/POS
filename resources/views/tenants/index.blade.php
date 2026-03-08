@extends('layouts.admin')

@section('title', __('Registered Shops'))

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-store-alt"></i>
            {{ __('Registered Grocery Shops') }}
        </div>
        <div class="page-subtitle">{{ __('Manage all business tenants on the cloud platform.') }}</div>
    </div>

    @if(session('success'))
        <div
            style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Shop Name') }}</th>
                        <th>{{ __('Owner Email') }}</th>
                        <th>{{ __('Branches') }}</th>
                        <th>{{ __('Staff') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Registered Date') }}</th>
                        <th style="text-align: right;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                        <tr>
                            <td style="font-weight: 700; color: var(--navy-dark);">{{ $tenant->name }}</td>
                            <td>{{ $tenant->email }}</td>
                            <td>{{ $tenant->branches_count }}</td>
                            <td>{{ $tenant->users_count }}</td>
                            <td>
                                <span class="status-badge {{ $tenant->status == 'active' ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $tenant->status == 'active' ? __('Active') : __('Suspended') }}
                                </span>
                            </td>
                            <td>{{ $tenant->created_at->format('Y-M-d') }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                    <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-secondary"
                                        style="padding: 6px 10px; font-size: 0.75rem;">
                                        <i class="fas fa-eye"></i> {{ __('View') }}
                                    </a>
                                    <form action="{{ route('tenants.update', $tenant->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        @if($tenant->status == 'active')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="btn"
                                                style="padding: 6px 10px; font-size: 0.75rem; background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2);">
                                                {{ __('Suspend') }}
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn"
                                                style="padding: 6px 10px; font-size: 0.75rem; background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2);">
                                                {{ __('Activate') }}
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection