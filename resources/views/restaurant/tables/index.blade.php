@extends('layouts.admin')

@section('title', 'Table Management')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-chair"></i>
            Table Management
        </div>
        <div class="page-subtitle">Manage restaurant tables and floor layout</div>
    </div>

    <div class="section animate-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title"><i class="fas fa-list"></i> Tables</h2>
            <a href="{{ route('restaurant.tables.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Table
            </a>
        </div>

        @if($tables->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--gray-500);">
                <i class="fas fa-chair" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p>No tables configured yet.</p>
                <a href="{{ route('restaurant.tables.create') }}" class="btn btn-primary" style="margin-top: 16px;">
                    <i class="fas fa-plus"></i> Create First Table
                </a>
            </div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Section</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tables as $table)
                            <tr>
                                <td><strong>{{ $table->name }}</strong></td>
                                <td>{{ $table->floor_section ?? 'N/A' }}</td>
                                <td>{{ $table->capacity }} seats</td>
                                <td>
                                    <span class="status-badge {{ $table->status }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst($table->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('restaurant.tables.edit', $table) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
