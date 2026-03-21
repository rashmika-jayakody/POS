@extends('layouts.admin')

@section('title', __('Business Locations'))

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-store"></i>
            {{ __('Business Locations') }}
        </div>
        <div class="page-subtitle">{{ __('Manage multiple physical branches of your grocery business.') }}</div>
        <div style="margin-top: 20px;">
            <a href="{{ route('branches.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Add New Location') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="animate-in"
            style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="animate-in"
            style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(239, 68, 68, 0.2);">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Branch Name') }}</th>
                        <th>{{ __('Address') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Staff Count') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th style="text-align: right;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $branch)
                        <tr>
                            <td style="font-weight: 700; color: var(--navy-dark);">{{ $branch->name }}</td>
                            <td>{{ $branch->address }}</td>
                            <td>{{ $branch->phone ?? __('N/A') }}</td>
                            <td>
                                <span
                                    style="background: var(--light-blue-bg); color: var(--light-blue); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                                    {{ __(':count members', ['count' => $branch->users_count]) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $branch->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $branch->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-secondary"
                                        style="padding: 6px 10px; font-size: 0.75rem;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST"
                                        onsubmit="return confirm('{{ __('Delete this location? This cannot be undone.') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn"
                                            style="padding: 6px 10px; font-size: 0.75rem; background: rgba(255, 107, 130, 0.1); color: var(--accent-coral); border: 1px solid rgba(255, 107, 130, 0.2);">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
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