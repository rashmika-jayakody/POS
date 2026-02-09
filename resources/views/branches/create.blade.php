@extends('layouts.admin')

@section('title', 'Add New Location')

@section('content')
    <div class="page-header animate-in" style="max-width: 800px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-plus-circle"></i>
            Add New Location
        </div>
        <div class="page-subtitle">Register a new physical shop for your business.</div>
    </div>

    <div class="section animate-in" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('branches.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Branch
                        Name</label>
                    <input type="text" name="name" required placeholder="e.g., Downtown Branch"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('name') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label
                        style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Address</label>
                    <input type="text" name="address" required placeholder="Full physical address"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('address') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Phone
                        Number (Optional)</label>
                    <input type="text" name="phone" placeholder="+1 234 567 890"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('phone') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div
                style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('branches.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Location</button>
            </div>
        </form>
    </div>
@endsection