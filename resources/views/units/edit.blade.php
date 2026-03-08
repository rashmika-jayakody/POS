@extends('layouts.admin')

@section('title', __('Edit Unit'))

@section('content')
    <div class="page-header animate-in" style="max-width: 800px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-edit"></i>
            {{ __('Edit Unit') }}
        </div>
        <div class="page-subtitle">{{ __('Update the unit name and short code.') }}</div>
    </div>

    <div class="section animate-in" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('units.update', $unit) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Unit name') }}</label>
                    <input type="text" name="name" required value="{{ old('name', $unit->name) }}"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('name')
                        <p style="font-size: 0.85rem; color: var(--danger); margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Short code') }}</label>
                    <input type="text" name="short_code" required value="{{ old('short_code', $unit->short_code) }}"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('short_code')
                        <p style="font-size: 0.85rem; color: var(--danger); margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('units.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Update Unit') }}</button>
            </div>
        </form>
    </div>
@endsection
