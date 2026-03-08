@extends('layouts.admin')

@section('title', __('Activity Log'))

@section('content')
    <div class="page-header animate-in">
        <div>
            <div class="page-title">
                <i class="fas fa-history"></i>
                {{ __('Activity Log') }}
            </div>
            <div class="page-subtitle">{{ __('Track actions by user, date and time.') }}</div>
        </div>
    </div>

    <div class="section animate-in">
        <form method="GET" action="{{ route('activity-logs.index') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; margin-bottom: 20px;">
            <div>
                <label for="user_id" style="display: block; font-size: 0.75rem; color: var(--gray-500); margin-bottom: 4px;">{{ __('User') }}</label>
                <select name="user_id" id="user_id" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; min-width: 180px;">
                    <option value="">{{ __('All users') }}</option>
                    @foreach($usersForFilter as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="log_type" style="display: block; font-size: 0.75rem; color: var(--gray-500); margin-bottom: 4px;">{{ __('Action type') }}</label>
                <select name="log_type" id="log_type" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; min-width: 160px;">
                    <option value="">{{ __('All types') }}</option>
                    @foreach($logTypes as $type)
                        <option value="{{ $type }}" {{ request('log_type') === $type ? 'selected' : '' }}>{{ str_replace('_', ' ', $type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_from" style="display: block; font-size: 0.75rem; color: var(--gray-500); margin-bottom: 4px;">{{ __('From date') }}</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px;">
            </div>
            <div>
                <label for="date_to" style="display: block; font-size: 0.75rem; color: var(--gray-500); margin-bottom: 4px;">{{ __('To date') }}</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px;">
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">
                    <i class="fas fa-filter"></i> {{ __('Filter') }}
                </button>
            </div>
        </form>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Date & time') }}</th>
                        <th>{{ __('User') }}</th>
                        @if(auth()->user()->hasRole('system_owner'))
                            <th>{{ __('Tenant') }}</th>
                        @endif
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="white-space: nowrap;">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td>{{ $log->user?->name ?? '—' }}<br><small style="color: var(--gray-500);">{{ $log->user?->email ?? '' }}</small></td>
                            @if(auth()->user()->hasRole('system_owner'))
                                <td>{{ $log->tenant?->name ?? '—' }}</td>
                            @endif
                            <td><code style="font-size: 0.8rem; background: var(--gray-100); padding: 2px 6px; border-radius: 4px;">{{ $log->log_type }}</code></td>
                            <td>{{ $log->description }}</td>
                            <td>
                                @if(!empty($log->properties))
                                    <details style="font-size: 0.8rem;">
                                        <summary style="cursor: pointer; color: var(--light-blue);">{{ __('View') }}</summary>
                                        <pre style="margin: 6px 0 0; padding: 8px; background: var(--gray-100); border-radius: 6px; max-width: 280px; overflow-x: auto; font-size: 0.75rem;">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('system_owner') ? 6 : 5 }}" style="text-align: center; color: var(--gray-500); padding: 40px;">{{ __('No activity logs found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div style="margin-top: 16px;">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
