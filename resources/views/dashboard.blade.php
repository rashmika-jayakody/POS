@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </div>
        <div class="page-subtitle">Welcome back, {{ auth()->user()->name }}! Here's your business overview.</div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid animate-in">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-label">Total Sales</div>
            <div class="stat-value">$24,500</div>
            <div class="stat-change positive"><i class="fas fa-arrow-up"></i> 12.5% from last week</div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-label">Active Users</div>
            <div class="stat-value">1,284</div>
            <div class="stat-change positive"><i class="fas fa-arrow-up"></i> 8.2% increase</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon"><i class="fas fa-box"></i></div>
            <div class="stat-label">In Stock</div>
            <div class="stat-value">3,592</div>
            <div class="stat-change negative"><i class="fas fa-arrow-down"></i> 2.1% decrease</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-icon"><i class="fas fa-store"></i></div>
            <div class="stat-label">Locations</div>
            <div class="stat-value">18</div>
            <div class="stat-change positive"><i class="fas fa-arrow-up"></i> 2 new added</div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-receipt"></i> Recent Transactions</h2>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#TXN-001024</td>
                        <td>John Anderson</td>
                        <td>$125.50</td>
                        <td>Feb 4, 2:15 PM</td>
                        <td><span class="status-badge active"><span class="status-dot"></span>Completed</span></td>
                        <td>Main Store</td>
                    </tr>
                    <tr>
                        <td>#TXN-001023</td>
                        <td>Sarah Mitchell</td>
                        <td>$89.99</td>
                        <td>Feb 4, 1:45 PM</td>
                        <td><span class="status-badge active"><span class="status-dot"></span>Completed</span></td>
                        <td>Downtown</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="grid-2 animate-in">
        <div class="section">
            <h2 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <a href="{{ route('cash-drawer.index') }}" class="btn btn-primary" style="text-align: center; justify-content: center;">
                    <i class="fas fa-cash-register"></i> Go to Cash Drawer
                </a>
                <a href="{{ route('users.create') }}" class="btn btn-primary" style="text-align: center; justify-content: center;">
                    <i class="fas fa-user-plus"></i> Add User
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-secondary" style="text-align: center; justify-content: center;">
                    <i class="fas fa-box-open"></i> Products & Stock
                </a>
                <button class="btn btn-secondary" type="button"><i class="fas fa-file-pdf"></i> Export Report</button>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title"><i class="fas fa-user-circle"></i> Active Users Today</h2>
            <div>
                <div class="list-item"
                    style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--gray-100);">
                    <div>
                        <h4 style="font-weight: 700; font-size: 0.9rem;">Jennifer Lee</h4>
                        <p style="font-size: 0.8rem; color: var(--gray-500);">Cashier - Main Store</p>
                    </div>
                    <span class="status-badge active"><span class="status-dot"></span>Online</span>
                </div>
                <div class="list-item"
                    style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                    <div>
                        <h4 style="font-weight: 700; font-size: 0.9rem;">Robert Garcia</h4>
                        <p style="font-size: 0.8rem; color: var(--gray-500);">Manager - Downtown</p>
                    </div>
                    <span class="status-badge active"><span class="status-dot"></span>Online</span>
                </div>
            </div>
        </div>
    </div>
@endsection