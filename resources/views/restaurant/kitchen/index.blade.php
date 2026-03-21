@extends('layouts.admin')

@section('title', 'Kitchen Display System')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-tv"></i>
            Kitchen Display System (KDS)
        </div>
        <div class="page-subtitle">Real-time order tracking for kitchen staff</div>
    </div>

    <div class="section animate-in">
        @if($orders->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--gray-500);">
                <i class="fas fa-tv" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p>No active orders in kitchen.</p>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                @foreach($orders as $order)
                    @php
                        $borderColor = match($order->status) {
                            'pending' => '#EF4444',
                            'confirmed' => '#4A9EFF',
                            'preparing' => '#F59E0B',
                            'ready' => '#10B981',
                            default => '#6B7280'
                        };
                        $bgColor = match($order->status) {
                            'pending' => 'rgba(239, 68, 68, 0.05)',
                            'confirmed' => 'rgba(74, 158, 255, 0.05)',
                            'preparing' => 'rgba(245, 158, 11, 0.05)',
                            'ready' => 'rgba(16, 185, 129, 0.05)',
                            default => '#F9FAFB'
                        };
                    @endphp
                    <div style="background: white; border-radius: 12px; padding: 20px; border: 3px solid {{ $borderColor }}; background: {{ $bgColor }};">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                            <div>
                                <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--navy-dark); margin-bottom: 4px;">{{ $order->order_no }}</h3>
                                <p style="color: var(--gray-500); font-size: 0.9rem;">
                                    @if($order->table)
                                        <i class="fas fa-chair"></i> Table: {{ $order->table->name }}
                                    @else
                                        <i class="fas fa-walking"></i> Takeout/Delivery
                                    @endif
                                    @if($order->user)
                                        · Waiter: {{ $order->user->name }}
                                    @endif
                                </p>
                                @if($order->confirmed_at)
                                    <p style="color: var(--gray-400); font-size: 0.8rem; margin-top: 4px;">
                                        <i class="fas fa-clock"></i> {{ $order->confirmed_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                            <span class="status-badge {{ $order->status }}" style="font-size: 0.75rem; font-weight: 700; padding: 6px 12px; background: {{ $borderColor }}; color: white; border-radius: 6px;">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div style="margin-top: 16px;">
                            <strong style="font-size: 0.85rem; color: var(--gray-600);">Items:</strong>
                            <ul style="margin-top: 8px; padding-left: 20px;">
                                @foreach($order->items as $item)
                                    <li style="margin-bottom: 6px; font-size: 0.9rem; display: flex; align-items: start; gap: 8px;">
                                        <span style="font-weight: 700; color: {{ $borderColor }};">{{ $item->qty }}x</span>
                                        <span>{{ $item->product->name }}</span>
                                        @if($item->special_instructions)
                                            <span style="color: var(--gray-500); font-size: 0.85rem; font-style: italic;">({{ $item->special_instructions }})</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--gray-200);">
                            @if($order->status === 'pending' || $order->status === 'confirmed')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'preparing')" class="btn btn-secondary" style="width: 100%; margin-bottom: 8px; background: #F59E0B; border-color: #F59E0B;">
                                    <i class="fas fa-fire"></i> Start Preparing
                                </button>
                            @endif
                            @if($order->status === 'preparing')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'ready')" class="btn btn-primary" style="width: 100%; background: #10B981; border-color: #10B981;">
                                    <i class="fas fa-check"></i> Mark Ready
                                </button>
                            @endif
                            @if($order->status === 'ready')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'served')" class="btn btn-primary" style="width: 100%; background: #4A9EFF; border-color: #4A9EFF;">
                                    <i class="fas fa-check-circle"></i> Mark Served
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function updateOrderStatus(orderId, status) {
            fetch(`/restaurant/orders/${orderId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
@endsection
