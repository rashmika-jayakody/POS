@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-users"></i>
            Customers
        </div>
        <div class="page-subtitle">Manage customer profiles and loyalty programs</div>
    </div>

    <div class="section animate-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title"><i class="fas fa-list"></i> Customers</h2>
            <a href="{{ route('restaurant.customers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Customer
            </a>
        </div>

        @if($customers->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--gray-500);">
                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p>No customers yet.</p>
            </div>
        @else
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Loyalty Points</th>
                            <th>Visits</th>
                            <th>Total Spent</th>
                            <th>Last Visit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td><strong>{{ $customer->name }}</strong></td>
                                <td>
                                    @if($customer->phone) {{ $customer->phone }}<br> @endif
                                    @if($customer->email) <small>{{ $customer->email }}</small> @endif
                                </td>
                                <td><strong style="color: var(--light-blue);">{{ number_format($customer->loyalty_points) }}</strong></td>
                                <td>{{ $customer->visit_count }}</td>
                                <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($customer->lifetime_spent, 2) }}</td>
                                <td>{{ $customer->last_visit_at ? $customer->last_visit_at->format('M d, Y') : 'Never' }}</td>
                                <td>
                                    <a href="{{ route('restaurant.customers.show', $customer) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 20px;">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection
