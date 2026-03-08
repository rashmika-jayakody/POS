@extends('layouts.admin')

@section('title', __('New Unit'))

@section('content')
    <div class="page-header animate-in" style="max-width: 800px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-plus-circle"></i>
            {{ __('Add New Unit') }}
        </div>
        <div class="page-subtitle">{{ __('Add a measurement unit for products (e.g. Kilogram, Pieces, Liters).') }}</div>
    </div>

    <div class="section animate-in" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('units.store', ['return' => $returnTo ?? null]) }}" method="POST">
            @csrf
            @if(!empty($returnTo))
                <input type="hidden" name="return" value="{{ $returnTo }}">
            @endif
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Unit name') }}</label>
                    <input type="text" name="name" required placeholder="{{ __('e.g. Kilogram, Pieces, Liters') }}"
                        value="{{ old('name') }}"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('name')
                        <p style="font-size: 0.85rem; color: var(--danger); margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Short code') }}</label>
                    <input type="text" name="short_code" required placeholder="{{ __('e.g. kg, pcs, L') }}"
                        value="{{ old('short_code') }}"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    <p style="font-size: 0.8rem; color: var(--gray-500); margin-top: 6px;">{{ __('Shown on products and reports.') }}</p>
                    @error('short_code')
                        <p style="font-size: 0.85rem; color: var(--danger); margin-top: 6px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                @if(!empty($returnTo) && $returnTo === 'products.create')
                    <a href="{{ route('products.create') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                @else
                    <a href="{{ route('units.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                @endif
                <button type="submit" class="btn btn-primary">{{ __('Create Unit') }}</button>
            </div>
        </form>
    </div>
@endsection
