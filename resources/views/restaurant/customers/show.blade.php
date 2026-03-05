@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-user"></i>
            {{ $customer->name }}
        </div>
        <div class="page-subtitle">Customer profile and history</div>
    </div>

    <div class="section animate-in">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">Contact Information</h3>
                @if($customer->phone)
                    <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                @endif
                @if($customer->email)
                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                @endif
                @if($customer->address)
                    <p><strong>Address:</strong> {{ $customer->address }}</p>
                @endif
                @if($customer->date_of_birth)
                    <p><strong>Date of Birth:</strong> {{ $customer->date_of_birth->format('M d, Y') }}</p>
                @endif
            </div>
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">Loyalty & Statistics</h3>
                <p><strong>Loyalty Points:</strong> <span style="color: var(--light-blue); font-weight: 700;">{{ number_format($customer->loyalty_points) }}</span></p>
                <p><strong>Total Visits:</strong> {{ $customer->visit_count }}</p>
                <p><strong>Lifetime Spent:</strong> {{ $currencySymbol ?? 'Rs' }}{{ number_format($customer->lifetime_spent, 2) }}</p>
                <p><strong>Last Visit:</strong> {{ $customer->last_visit_at ? $customer->last_visit_at->format('M d, Y') : 'Never' }}</p>
            </div>
        </div>

        @if($customer->dietary_preferences)
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">Dietary Preferences</h3>
                <p>{{ $customer->dietary_preferences }}</p>
            </div>
        @endif

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <a href="{{ route('restaurant.customers.edit', $customer) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Customer
            </a>
            <a href="{{ route('restaurant.customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
@endsection
