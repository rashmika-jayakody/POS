@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-clipboard-list"></i>
            Orders
        </div>
        <div class="page-subtitle">View and manage restaurant orders</div>
    </div>

    <div class="section animate-in">
        @if($orders->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--gray-500);">
                <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p>No orders yet.</p>
            </div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order No</th>
                            <th>Table</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_no }}</strong></td>
                                <td>{{ $order->table?->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</td>
                                <td>
                                    <span class="status-badge {{ $order->status }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td><strong>{{ $currencySymbol ?? 'Rs' }}{{ number_format($order->grand_total, 2) }}</strong></td>
                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('restaurant.orders.show', $order) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
