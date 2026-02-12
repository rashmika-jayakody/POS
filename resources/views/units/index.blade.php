@extends('layouts.admin')

@section('title', 'Units')

@section('content')
    <div class="page-header animate-in">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="page-title">
                    <i class="fas fa-balance-scale"></i>
                    Units
                </div>
                <div class="page-subtitle">Manage measurement units for your products (e.g. kg, pcs, L).</div>
            </div>
            <a href="{{ route('units.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                New Unit
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
                        <th>Name</th>
                        <th>Short code</th>
                        <th>Products</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                        <tr>
                            <td style="font-weight: 700; color: var(--navy-dark);">{{ $unit->name }}</td>
                            <td style="font-family: monospace; color: var(--gray-500);">{{ $unit->short_code }}</td>
                            <td>{{ $unit->products_count }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('units.edit', $unit) }}" class="btn btn-secondary"
                                        style="padding: 6px 10px; font-size: 0.75rem;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('units.destroy', $unit) }}" method="POST"
                                        onsubmit="return confirm('Delete this unit?');">
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
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--gray-500);">
                                No units yet. <a href="{{ route('units.create') }}" style="color: var(--light-blue);">Add your first unit</a> so you can assign them to products.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
