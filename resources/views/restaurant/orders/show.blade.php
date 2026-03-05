@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-clipboard-list"></i>
            Order: {{ $order->order_no }}
        </div>
        <div class="page-subtitle">View order details</div>
    </div>

    <div class="section animate-in">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">Order Information</h3>
                <p><strong>Order No:</strong> {{ $order->order_no }}</p>
                <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</p>
                <p><strong>Status:</strong> 
                    <span class="status-badge {{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
                <p><strong>Table:</strong> {{ $order->table?->name ?? 'N/A' }}</p>
                <p><strong>Server:</strong> {{ $order->user?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">Totals</h3>
                <p><strong>Subtotal:</strong> {{ $currencySymbol ?? 'Rs' }}{{ number_format($order->subtotal, 2) }}</p>
                <p><strong>Tax:</strong> {{ $currencySymbol ?? 'Rs' }}{{ number_format($order->tax_total, 2) }}</p>
                <p><strong>Service Charge:</strong> {{ $currencySymbol ?? 'Rs' }}{{ number_format($order->service_charge, 2) }}</p>
                <p style="font-size: 1.2rem; font-weight: 700; margin-top: 12px; padding-top: 12px; border-top: 2px solid var(--gray-100);">
                    <strong>Total:</strong> {{ $currencySymbol ?? 'Rs' }}{{ number_format($order->grand_total, 2) }}
                </p>
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;">Order Items</h3>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    {{ $item->product->name }}
                                    @if($item->special_instructions)
                                        <br><small style="color: var(--gray-500);">{{ $item->special_instructions }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <a href="{{ route('restaurant.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>
@endsection
