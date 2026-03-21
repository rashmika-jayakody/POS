@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-user-edit"></i>
            Edit Customer
        </div>
        <div class="page-subtitle">Update customer information</div>
    </div>

    <div class="section animate-in">
        <form action="{{ route('restaurant.customers.update', $customer) }}" method="POST">
            @csrf
            @method('PATCH')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
                    @error('phone') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}">
                    @error('email') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}">
                    @error('date_of_birth') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="2">{{ old('address', $customer->address) }}</textarea>
                @error('address') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="dietary_preferences">Dietary Preferences</label>
                <textarea id="dietary_preferences" name="dietary_preferences" rows="3" 
                          placeholder="e.g. Vegetarian, Allergies, etc.">{{ old('dietary_preferences', $customer->dietary_preferences) }}</textarea>
                @error('dietary_preferences') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Internal notes about this customer">{{ old('notes', $customer->notes) }}</textarea>
                @error('notes') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Customer
                </button>
                <a href="{{ route('restaurant.customers.index') }}" class="btn btn-secondary">
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
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E2E8F0;
            border-radius: var(--radius-md, 12px);
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus {
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
