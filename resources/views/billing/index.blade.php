@extends('layouts.admin')

@section('title', __('Billing & Subscription'))

@push('styles')
<style>
.billing-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 16px;
}
.billing-header h1 {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--navy-dark);
}
.plan-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}
.plan-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 28px;
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 2px solid transparent;
}
.plan-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}
.plan-card.active {
    border-color: var(--light-blue);
    background: linear-gradient(135deg, rgba(74, 158, 255, 0.04) 0%, rgba(0, 201, 183, 0.02) 100%);
}
.plan-card.active::before {
    content: 'Current Plan';
    position: absolute;
    top: -1px;
    right: 20px;
    background: var(--light-blue);
    color: var(--white);
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 0 0 var(--radius-sm) var(--radius-sm);
}
.plan-name {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 4px;
}
.plan-tagline {
    font-size: 0.85rem;
    color: var(--gray-500);
    margin-bottom: 16px;
}
.plan-price {
    font-size: 2rem;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 4px;
}
.plan-price span {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--gray-400);
}
.plan-features {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}
.plan-features li {
    padding: 6px 0;
    font-size: 0.9rem;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 8px;
}
.plan-features li i.fa-check {
    color: var(--success);
}
.plan-features li i.fa-times {
    color: var(--gray-300);
}
.plan-features li.excluded {
    color: var(--gray-300);
    text-decoration: line-through;
}
.btn-plan {
    display: block;
    width: 100%;
    padding: 12px;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 0.95rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}
.btn-plan-primary {
    background: linear-gradient(135deg, var(--light-blue), #3B82F6);
    color: var(--white);
}
.btn-plan-primary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}
.btn-plan-secondary {
    background: var(--gray-100);
    color: var(--navy-dark);
}
.btn-plan-secondary:hover {
    background: var(--gray-300);
}
.btn-plan-current {
    background: transparent;
    color: var(--light-blue);
    border: 2px solid var(--light-blue);
    cursor: default;
}
.subscription-status {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 28px;
    margin-bottom: 32px;
}
.subscription-status h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--navy-dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}
.status-item {
    padding: 12px 16px;
    background: var(--gray-light);
    border-radius: var(--radius-md);
}
.status-label {
    font-size: 0.8rem;
    color: var(--gray-500);
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 4px;
}
.status-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--navy-dark);
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}
.status-badge.active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}
.status-badge.trialing {
    background: rgba(74, 158, 255, 0.1);
    color: var(--light-blue);
}
.status-badge.past_due {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}
.status-badge.cancelled, .status-badge.expired {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}
.status-badge.pending_payment {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}
.payment-complete-banner {
    background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
    border: 2px solid var(--warning);
    border-radius: var(--radius-lg);
    padding: 32px;
    margin-bottom: 32px;
    text-align: center;
}
.payment-complete-banner h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--navy-dark);
    margin-bottom: 8px;
}
.payment-complete-banner p {
    color: var(--gray-500);
    margin-bottom: 20px;
}
.payment-complete-banner .btn-pay-now {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    background: linear-gradient(135deg, var(--light-blue), #3B82F6);
    color: var(--white);
    border: none;
    border-radius: var(--radius-md);
    font-size: 1rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}
.payment-complete-banner .btn-pay-now:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}
.payment-history {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 28px;
}
.payment-history h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--navy-dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.payment-table {
    width: 100%;
    border-collapse: collapse;
}
.payment-table th {
    text-align: left;
    padding: 12px 16px;
    font-size: 0.8rem;
    color: var(--gray-500);
    font-weight: 600;
    text-transform: uppercase;
    border-bottom: 2px solid var(--gray-100);
}
.payment-table td {
    padding: 12px 16px;
    font-size: 0.9rem;
    color: var(--navy-dark);
    border-bottom: 1px solid var(--gray-100);
}
.payment-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}
.payment-status.successful { background: rgba(16, 185, 129, 0.1); color: var(--success); }
.payment-status.pending { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
.payment-status.failed { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
.payment-status.refunded { background: rgba(107, 180, 255, 0.1); color: var(--light-blue); }
.cancel-section {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--gray-100);
    text-align: center;
}
.no-subscription {
    text-align: center;
    padding: 40px 20px;
    color: var(--gray-500);
}
.no-subscription i {
    font-size: 3rem;
    color: var(--gray-300);
    margin-bottom: 16px;
}
.no-subscription h3 {
    font-size: 1.1rem;
    color: var(--navy-dark);
    margin-bottom: 8px;
}
</style>
@endpush

@section('content')
<div class="page-header animate-in">
    <div class="billing-header">
        <div>
            <h1><i class="fas fa-credit-card"></i> Billing & Subscription</h1>
            <p style="color: var(--gray-500); margin-top: 4px;">Manage your subscription plan and billing details.</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 12px 20px; border-radius: var(--radius-md); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('warning'))
    <div style="background: rgba(245, 158, 11, 0.1); color: var(--warning); padding: 12px 20px; border-radius: var(--radius-md); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
    </div>
@endif

@if(session('error'))
    <div style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 12px 20px; border-radius: var(--radius-md); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-times-circle"></i> {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 12px 20px; border-radius: var(--radius-md); margin-bottom: 20px;">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if(session('info'))
    <div style="background: rgba(74, 158, 255, 0.1); color: var(--light-blue); padding: 12px 20px; border-radius: var(--radius-md); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-info-circle"></i> {{ session('info') }}
    </div>
@endif

@php
    $currentPlan = $tenant->plan ?? 'professional';
    $plans = config('plans', []);
@endphp

@if($subscription && $subscription->isPendingPayment())
<div class="section animate-in">
    <div class="payment-complete-banner">
        <i class="fas fa-exclamation-circle" style="font-size: 2.5rem; color: var(--warning); margin-bottom: 12px;"></i>
        <h2>Complete Your Payment</h2>
        <p>Your account is set up! Please complete your payment to activate your <strong>{{ $plans[$subscription->plan]['name'] ?? ucfirst($subscription->plan) }}</strong> subscription.</p>
        <a href="{{ route('billing.checkout', $subscription->plan) }}" class="btn-pay-now">
            <i class="fas fa-lock"></i> Pay Now — {{ $plans[$subscription->plan]['price_display'] ?? 'LKR ' . number_format($plans[$subscription->plan]['price_lkr'] ?? 0) }}/month
        </a>
    </div>
</div>
@endif

<div class="section animate-in">
    <div class="plan-cards">
        @foreach($plans as $key => $plan)
            @php
                $isCurrentPlan = $key === $currentPlan;
                $isActiveSubscribed = $subscription && $subscription->isActive() && $subscription->plan === $key;
            @endphp
            <div class="plan-card {{ $isCurrentPlan ? 'active' : '' }}">
                <div class="plan-name">{{ $plan['name'] }}</div>
                <div class="plan-tagline">{{ $plan['tagline'] ?? '' }}</div>
                <div class="plan-price">
                    {{ $plan['price_display'] ?? 'LKR ' . number_format($plan['price_lkr']) }}<span>/month</span>
                </div>
                <ul class="plan-features">
                    @foreach($plan['features'] ?? [] as $feature)
                        <li><i class="fas fa-check"></i> {{ $feature }}</li>
                    @endforeach
                    @foreach($plan['excluded'] ?? [] as $excluded)
                        <li class="excluded"><i class="fas fa-times"></i> {{ $excluded }}</li>
                    @endforeach
                </ul>

                @if($isCurrentPlan && $subscription && $subscription->isActive())
                    <div class="btn-plan btn-plan-current"><i class="fas fa-check"></i> Current Plan</div>
                @elseif($isCurrentPlan && (!$subscription || !$subscription->isActive()))
                    <div class="btn-plan btn-plan-current" style="border-color: var(--warning); color: var(--warning);"><i class="fas fa-exclamation-triangle"></i> Inactive</div>
                @else
                    <a href="{{ route('billing.switch', $key) }}" class="btn-plan btn-plan-primary" onclick="return confirm('Switch to {{ $plan['name'] }} plan? You will be redirected to payment.')">
                        @if($subscription && $subscription->isActive()) Upgrade @else Subscribe @endif
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</div>

@if($subscription)
<div class="section animate-in">
    <div class="subscription-status">
        <h2><i class="fas fa-info-circle" style="color: var(--light-blue);"></i> Subscription Status</h2>
        <div class="status-grid">
            <div class="status-item">
                <div class="status-label">Status</div>
                <div class="status-value">
                    <span class="status-badge {{ $subscription->status }}">
                        @switch($subscription->status)
                            @case('active') <i class="fas fa-check-circle"></i> Active @break
                            @case('trialing') <i class="fas fa-clock"></i> Trialing @break
                            @case('pending_payment') <i class="fas fa-exclamation-circle"></i> Pending Payment @break
                            @case('past_due') <i class="fas fa-exclamation-triangle"></i> Past Due @break
                            @case('cancelled') <i class="fas fa-times-circle"></i> Cancelled @break
                            @case('expired') <i class="fas fa-ban"></i> Expired @break
                            @default {{ ucfirst($subscription->status) }}
                        @endswitch
                    </span>
                </div>
            </div>
            <div class="status-item">
                <div class="status-label">Plan</div>
                <div class="status-value">{{ $plans[$subscription->plan]['name'] ?? ucfirst($subscription->plan) }}</div>
            </div>
            <div class="status-item">
                <div class="status-label">Started</div>
                <div class="status-value">{{ $subscription->starts_at?->format('M d, Y') ?? 'N/A' }}</div>
            </div>
            @if($subscription->trial_ends_at)
                <div class="status-item">
                    <div class="status-label">Trial Ends</div>
                    <div class="status-value">{{ $subscription->trial_ends_at->format('M d, Y') }}</div>
                </div>
            @endif
            @if($subscription->ends_at)
                <div class="status-item">
                    <div class="status-label">{{ $subscription->cancelled_at ? 'Expires' : 'Next Billing' }}</div>
                    <div class="status-value">{{ $subscription->ends_at->format('M d, Y') }}</div>
                </div>
            @endif
        </div>

        @if($subscription->isActive() || $subscription->isOnTrial())
            <div class="cancel-section">
                <form method="POST" action="{{ route('billing.cancel-subscription') }}">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn-plan btn-plan-secondary" style="width: auto; display: inline-flex; align-items: center; gap: 6px; padding: 10px 24px;" onclick="return confirm('Are you sure you want to cancel your subscription? You will lose access at the end of your billing period.')">
                        <i class="fas fa-times"></i> Cancel Subscription
                    </button>
                </form>
            </div>
        @elseif($subscription->isPendingPayment())
            <div class="cancel-section">
                <a href="{{ route('billing.checkout', $subscription->plan) }}" class="btn-plan btn-plan-primary" style="width: auto; display: inline-flex; align-items: center; gap: 6px; padding: 10px 24px; text-decoration: none;">
                    <i class="fas fa-lock"></i> Complete Payment Now
                </a>
            </div>
        @endif
    </div>
</div>
@endif

<div class="section animate-in">
    <div class="payment-history">
        <h2><i class="fas fa-history" style="color: var(--light-blue);"></i> Payment History</h2>
        @if($payments->count() > 0)
            <div style="overflow-x: auto;">
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->paid_at?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}</td>
                                <td>{{ ucfirst($payment->plan ?? 'N/A') }}</td>
                                <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                                <td><span class="payment-status {{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 16px;">
                {{ $payments->withQueryString()->links() }}
            </div>
        @else
            <div class="no-subscription">
                <i class="fas fa-receipt"></i>
                <h3>No payments yet</h3>
                <p>Your payment history will appear here once you subscribe to a plan.</p>
            </div>
        @endif
    </div>
</div>
@endsection