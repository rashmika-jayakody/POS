@extends('layouts.admin')

@section('title', 'Reservation Details')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-calendar-check"></i>
            Reservation: {{ $reservation->reservation_no }}
        </div>
        <div class="page-subtitle">View reservation details</div>
    </div>

    <div class="section animate-in">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">Customer Information</h3>
                <p><strong>Name:</strong> {{ $reservation->customer_name }}</p>
                <p><strong>Phone:</strong> {{ $reservation->customer_phone }}</p>
                @if($reservation->customer_email)
                    <p><strong>Email:</strong> {{ $reservation->customer_email }}</p>
                @endif
            </div>
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">Reservation Details</h3>
                <p><strong>Date & Time:</strong> {{ $reservation->reservation_date->format('M d, Y H:i') }}</p>
                <p><strong>Guests:</strong> {{ $reservation->guest_count }}</p>
                <p><strong>Table:</strong> {{ $reservation->table?->name ?? 'Not Assigned' }}</p>
                <p><strong>Status:</strong> 
                    <span class="status-badge {{ $reservation->status }}">
                        {{ ucfirst($reservation->status) }}
                    </span>
                </p>
            </div>
        </div>

        @if($reservation->special_requests)
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">Special Requests</h3>
                <p>{{ $reservation->special_requests }}</p>
            </div>
        @endif

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <a href="{{ route('restaurant.reservations.edit', $reservation) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Reservation
            </a>
            <a href="{{ route('restaurant.reservations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
@endsection
