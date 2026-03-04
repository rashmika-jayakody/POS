@extends('layouts.admin')

@section('title', 'Edit Location')

@section('content')
    <div class="page-header animate-in" style="max-width: 800px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-edit"></i>
            Edit Location: {{ $branch->name }}
        </div>
        <div class="page-subtitle">Update physical shop details and status.</div>
    </div>

    <div class="section animate-in" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('branches.update', $branch->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Branch Name</label>
                    <input type="text" name="name" value="{{ old('name', $branch->name) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('name') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Address</label>
                    <input type="text" name="address" value="{{ old('address', $branch->address) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('address') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Phone Number (Optional)</label>
                    <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('phone') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $branch->is_active ? 'checked' : '' }} 
                        style="width: 18px; height: 18px;">
                    <label for="is_active" style="font-weight: 600; color: var(--gray-700);">Location is active and operational</label>
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Location</button>
            </div>
        </form>
    </div>
@endsection
