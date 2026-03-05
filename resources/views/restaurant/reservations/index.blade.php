@extends('layouts.admin')

@section('title', 'Reservations')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-calendar-check"></i>
            Reservations
        </div>
        <div class="page-subtitle">Manage restaurant reservations</div>
    </div>

    <div class="section animate-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title"><i class="fas fa-list"></i> Reservations</h2>
            <a href="{{ route('restaurant.reservations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Reservation
            </a>
        </div>

        @if($reservations->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--gray-500);">
                <i class="fas fa-calendar-check" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p>No reservations yet.</p>
            </div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Reservation No</th>
                            <th>Customer</th>
                            <th>Table</th>
                            <th>Date & Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $reservation)
                            <tr>
                                <td><strong>{{ $reservation->reservation_no }}</strong></td>
                                <td>{{ $reservation->customer_name }}<br><small>{{ $reservation->customer_phone }}</small></td>
                                <td>{{ $reservation->table?->name ?? 'Not Assigned' }}</td>
                                <td>{{ $reservation->reservation_date->format('M d, Y H:i') }}</td>
                                <td>{{ $reservation->guest_count }}</td>
                                <td>
                                    <span class="status-badge {{ $reservation->status }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('restaurant.reservations.show', $reservation) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>
@endsection
