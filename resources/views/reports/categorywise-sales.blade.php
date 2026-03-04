@extends('layouts.admin')

@section('title', 'Categorywise Sales')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title"><i class="fas fa-tags"></i> Categorywise Sales</div>
        <div class="page-subtitle">Sales grouped by category for the selected period.</div>
    </div>

    @include('reports.partials.filter', ['showDate' => true, 'showBranch' => true, 'f' => $f, 'branches' => $branches, 'routeName' => 'reports.categorywise-sales'])

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Qty sold</th>
                        <th>Net sales</th>
                        <th>COGS</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r['category_name'] }}</td>
                            <td>{{ number_format($r['total_qty'], 2) }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['net_sales'], 2) }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['cogs'], 2) }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['profit'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; color: var(--gray-500); padding: 24px;">No sales in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
