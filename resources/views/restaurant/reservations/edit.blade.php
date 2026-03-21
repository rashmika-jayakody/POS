@extends('layouts.admin')

@section('title', 'Edit Reservation')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-calendar-check"></i>
            Edit Reservation
        </div>
        <div class="page-subtitle">Update reservation information</div>
    </div>

    <div class="section animate-in">
        <form action="{{ route('restaurant.reservations.update', $reservation) }}" method="POST">
            @csrf
            @method('PATCH')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="customer_name">Customer Name *</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $reservation->customer_name) }}" required>
                    @error('customer_name') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="customer_phone">Phone *</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $reservation->customer_phone) }}" required>
                    @error('customer_phone') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="customer_email">Email</label>
                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', $reservation->customer_email) }}">
                    @error('customer_email') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="restaurant_table_id">Table</label>
                    <select id="restaurant_table_id" name="restaurant_table_id">
                        <option value="">Not Assigned</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ old('restaurant_table_id', $reservation->restaurant_table_id) == $table->id ? 'selected' : '' }}>
                                {{ $table->name }} ({{ $table->floor_section ?? 'N/A' }}) - {{ $table->capacity }} seats
                            </option>
                        @endforeach
                    </select>
                    @error('restaurant_table_id') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="reservation_date">Date & Time *</label>
                    <input type="datetime-local" id="reservation_date" name="reservation_date" 
                           value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d\TH:i')) }}" required>
                    @error('reservation_date') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="guest_count">Number of Guests *</label>
                    <input type="number" id="guest_count" name="guest_count" value="{{ old('guest_count', $reservation->guest_count) }}" 
                           required min="1" max="50">
                    @error('guest_count') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="pending" {{ old('status', $reservation->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status', $reservation->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="seated" {{ old('status', $reservation->status) === 'seated' ? 'selected' : '' }}>Seated</option>
                        <option value="completed" {{ old('status', $reservation->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $reservation->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ old('status', $reservation->status) === 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                    @error('status') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="special_requests">Special Requests</label>
                <textarea id="special_requests" name="special_requests" rows="3">{{ old('special_requests', $reservation->special_requests) }}</textarea>
                @error('special_requests') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Reservation
                </button>
                <a href="{{ route('restaurant.reservations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <style>
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E2E8F0;
            border-radius: var(--radius-md, 12px);
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--light-blue);
            box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.2);
        }
        .error {
            font-size: 0.85rem;
            color: #DC2626;
            margin-top: 6px;
        }
    </style>
@endsection
