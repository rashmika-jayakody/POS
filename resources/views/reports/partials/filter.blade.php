@php
    $showDate = $showDate ?? true;
    $showBranch = $showBranch ?? true;
    $showPeriod = $showPeriod ?? false;
    $showType = $showType ?? false;
    $f = $f ?? [];
    $routeName = $routeName ?? request()->route()->getName();
@endphp
<form method="get" action="{{ route($routeName) }}" class="report-filter-form" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; margin-bottom: 20px;">
    @if($showDate)
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--gray-500); margin-bottom: 4px;">{{ __('From') }}</label>
            <input type="date" name="from" value="{{ $f['from'] ?? '' }}" class="filter-input" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem;">
        </div>
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--gray-500); margin-bottom: 4px;">{{ __('To') }}</label>
            <input type="date" name="to" value="{{ $f['to'] ?? '' }}" class="filter-input" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem;">
        </div>
    @endif
    @if($showBranch)
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--gray-500); margin-bottom: 4px;">{{ __('Branch') }}</label>
            <select name="branch_id" class="filter-input" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem; min-width: 160px;">
                <option value="">{{ __('All branches') }}</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ isset($f['branch_id']) && $f['branch_id'] == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    @if($showPeriod)
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--gray-500); margin-bottom: 4px;">{{ __('Group by') }}</label>
            <select name="period" class="filter-input" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem;">
                <option value="day" {{ ($period ?? '') === 'day' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                <option value="week" {{ ($period ?? '') === 'week' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                <option value="month" {{ ($period ?? '') === 'month' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
            </select>
        </div>
    @endif
    @if($showType && isset($type))
        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--gray-500); margin-bottom: 4px;">{{ __('Show') }}</label>
            <select name="type" class="filter-input" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.9rem;">
                <option value="expired" {{ ($type ?? '') === 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                <option value="soon" {{ ($type ?? '') === 'soon' ? 'selected' : '' }}>{{ __('Expiring soon (30 days)') }}</option>
            </select>
        </div>
    @endif
    <button type="submit" class="btn btn-primary" style="padding: 8px 18px;">
        <i class="fas fa-filter"></i> {{ __('Apply') }}
    </button>
</form>
