@extends('layouts.admin')

@section('title', __('Edit Category'))

@section('content')
    <div class="page-header animate-in" style="max-width: 800px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-edit"></i>
            {{ __('Edit Category: :name', ['name' => $category->name]) }}
        </div>
        <div class="page-subtitle">{{ __('Modify category details and visibility.') }}</div>
    </div>

    <div class="section animate-in" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Category Name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                </div>

                <div>
                    <label
                        style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Description') }}</label>
                    <textarea name="description" rows="4"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; resize: none;">{{ old('description', $category->description) }}</textarea>
                </div>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="is_active" id="is_active" {{ $category->is_active ? 'checked' : '' }}
                        style="width: 18px; height: 18px;">
                    <label for="is_active" style="font-weight: 600; color: var(--gray-700);">{{ __('Category is active and visible') }}</label>
                </div>
            </div>

            <div
                style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Update Category') }}</button>
            </div>
        </form>
    </div>
@endsection