@extends('layouts.admin')

@section('title', $title)

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-chart-line"></i>
            {{ $title }}
        </div>
        @if (!empty($description))
            <div class="page-subtitle">{{ $description }}</div>
        @endif
    </div>

    <div class="section animate-in">
        <p style="color: var(--gray-500); font-size: 0.95rem;">
            This report screen is ready. The detailed report logic and filters will be implemented next.
        </p>
    </div>
@endsection

