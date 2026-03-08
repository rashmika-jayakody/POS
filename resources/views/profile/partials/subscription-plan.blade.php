@php
    $tenant = auth()->user()->tenant;
    $planKey = $tenant?->plan ?? 'professional';
    $plans = config('plans', []);
    $planInfo = $plans[$planKey] ?? $plans['professional'] ?? null;
@endphp
@if($planInfo)
<div class="profile-section">
    <div class="settings-card-title" style="margin-bottom: 6px;"><i class="fas fa-crown"></i> {{ __('Subscription Plan') }}</div>
    <div class="settings-card-subtitle" style="margin-bottom: 20px;">{{ __('Your current plan and included features.') }}</div>

    <div class="subscription-plan-card" style="background: linear-gradient(135deg, rgba(74, 158, 255, 0.08) 0%, rgba(0, 201, 183, 0.06) 100%); border: 1px solid rgba(74, 158, 255, 0.2); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 20px;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--navy-dark); margin: 0 0 4px 0;">{{ $planInfo['name'] }}</h3>
                @if(!empty($planInfo['tagline']))
                    <p style="font-size: 0.9rem; color: var(--gray-500); margin: 0;">{{ $planInfo['tagline'] }}</p>
                @endif
            </div>
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--light-blue);">
                LKR {{ number_format($planInfo['price_lkr'] ?? 0) }}<span style="font-size: 0.85rem; font-weight: 600; color: var(--gray-500);">/{{ __('month') }}</span>
            </div>
        </div>

        <div class="subscription-plan-details">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--navy-dark); margin-bottom: 10px;">{{ __('Plan details') }}</div>
            <ul style="margin: 0; padding-left: 20px; color: var(--gray-700); font-size: 0.9rem; line-height: 1.7;">
                @foreach($planInfo['features'] ?? [] as $feature)
                    <li><i class="fas fa-check" style="color: var(--success); margin-right: 8px;"></i> {{ $feature }}</li>
                @endforeach
                @foreach($planInfo['excluded'] ?? [] as $excluded)
                    <li style="color: var(--gray-400);"><i class="fas fa-times" style="margin-right: 8px;"></i> <del>{{ $excluded }}</del></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
