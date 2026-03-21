@extends('layouts.admin')

@section('title', 'Create Table')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-chair"></i>
            Create Table
        </div>
        <div class="page-subtitle">Add a new table to your restaurant</div>
    </div>

    <div class="section animate-in">
        <form action="{{ route('restaurant.tables.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="name">Table Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Table 1, Booth 5">
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="floor_section">Floor Section</label>
                    <input type="text" id="floor_section" name="floor_section" value="{{ old('floor_section') }}"
                           placeholder="e.g. Main Dining, Patio, VIP">
                    @error('floor_section') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="capacity">Capacity (seats) *</label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 4) }}" required
                           min="1" max="50">
                    @error('capacity') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="position_x">Position X (for layout)</label>
                    <input type="number" id="position_x" name="position_x" value="{{ old('position_x') }}">
                    @error('position_x') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="position_y">Position Y (for layout)</label>
                    <input type="number" id="position_y" name="position_y" value="{{ old('position_y') }}">
                    @error('position_y') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Additional notes about this table">{{ old('notes') }}</textarea>
                @error('notes') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Table
                </button>
                <a href="{{ route('restaurant.tables.index') }}" class="btn btn-secondary">
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
