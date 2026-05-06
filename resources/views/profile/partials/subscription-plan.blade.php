@php
    $tenant = auth()->user()->tenant;
    $planKey = $tenant?->plan ?? 'professional';
    $plans = config('plans', []);
    $planInfo = $plans[$planKey] ?? $plans['professional'] ?? null;
    $subscription = $tenant?->currentSubscription;
@endphp
@if($planInfo)
<div class="profile-section">
    <div class="settings-card-title" style="margin-bottom: 6px;"><i class="fas fa-crown"></i> {{ __('Subscription Plan') }}</div>
    <div class="settings-card-subtitle" style="margin-bottom: 20px;">{{ __('Your current plan and included features.') }}</div>

    <div class="subscription-plan-card" style="background: linear-gradient(135deg, rgba(74, 158, 255, 0.08) 0%, rgba(0, 201, 183, 0.06) 100%); border: 1px solid rgba(74, 158, 255, 0.2); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 20px;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--navy-dark); margin: 0 0 4px 0;">
                    {{ $planInfo['name'] }}
                    @if($subscription)
                        @if($subscription->isActive())
                            <span style="font-size: 0.75rem; background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 2px 8px; border-radius: 12px; margin-left: 8px;">Active</span>
                        @elseif($subscription->isOnTrial())
                            <span style="font-size: 0.75rem; background: rgba(74, 158, 255, 0.1); color: var(--light-blue); padding: 2px 8px; border-radius: 12px; margin-left: 8px;">Trial</span>
                        @elseif($subscription->isPendingPayment())
                            <span style="font-size: 0.75rem; background: rgba(245, 158, 11, 0.1); color: var(--warning); padding: 2px 8px; border-radius: 12px; margin-left: 8px;">Awaiting Payment</span>
                        @elseif($subscription->isPastDue())
                            <span style="font-size: 0.75rem; background: rgba(245, 158, 11, 0.1); color: var(--warning); padding: 2px 8px; border-radius: 12px; margin-left: 8px;">Past Due</span>
                        @endif
                    @endif
                </h3>
                @if(!empty($planInfo['tagline']))
                    <p style="font-size: 0.9rem; color: var(--gray-500); margin: 0;">{{ $planInfo['tagline'] }}</p>
                @endif
            </div>
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--light-blue);">
                {{ $planInfo['price_display'] ?? 'LKR ' . number_format($planInfo['price_lkr']) }}<span style="font-size: 0.85rem; font-weight: 600; color: var(--gray-500);">/{{ __('month') }}</span>
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

        @if($subscription && $subscription->isPendingPayment())
            <div style="margin-top: 16px; padding: 12px; background: rgba(245, 158, 11, 0.08); border-radius: var(--radius-md); font-size: 0.85rem; color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.3);">
                <i class="fas fa-exclamation-circle"></i> Your subscription is awaiting payment. <a href="{{ route('billing.checkout', $subscription->plan) }}" style="color: var(--light-blue); font-weight: 600; text-decoration: underline;">Complete payment now</a> to activate your account.
            </div>
        @endif

        <div style="margin-top: 16px;">
            <a href="{{ route('billing.index') }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: linear-gradient(135deg, var(--light-blue), #3B82F6); color: white; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; text-decoration: none;">
                <i class="fas fa-credit-card"></i> Manage Billing
            </a>
        </div>
    </div>
</div>
@endif