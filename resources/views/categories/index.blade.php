@extends('layouts.admin')

@section('title', 'Product Categories')

@section('content')
    <div class="page-header animate-in">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="page-title">
                    <i class="fas fa-tags"></i>
                    Product Categories
                </div>
                <div class="page-subtitle">Organize your products into logical groups for better management.</div>
            </div>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                New Category
            </a>
        </div>
    </div>

    @if(session('success'))
        <div
            style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div
            style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(239, 68, 68, 0.2);">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Products Count</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td style="font-weight: 700; color: var(--navy-dark);">{{ $category->name }}</td>
                            <td style="font-family: monospace; color: var(--gray-500);">{{ $category->slug }}</td>
                            <td>{{ Str::limit($category->description, 50) }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td>
                                <span class="status-badge {{ $category->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-secondary"
                                        style="padding: 6px 10px; font-size: 0.75rem;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn"
                                            style="padding: 6px 10px; font-size: 0.75rem; background: rgba(255, 107, 130, 0.1); color: var(--accent-coral); border: 1px solid rgba(255, 107, 130, 0.2);">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection