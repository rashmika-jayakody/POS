@extends('layouts.admin')

@section('title', 'Expiry Tracking')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title"><i class="fas fa-exclamation-triangle"></i> Expiry Tracking</div>
        <div class="page-subtitle">Expired or expiring soon stock by batch.</div>
    </div>

    @include('reports.partials.filter', ['showDate' => false, 'showBranch' => true, 'showType' => true, 'type' => $type, 'branches' => $branches, 'routeName' => 'reports.expiry-tracking'])

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Branch</th>
                        <th>Batch</th>
                        <th>Quantity</th>
                        <th>Expiry date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ $row->product->name ?? '-' }}</td>
                            <td>{{ $row->branch->name ?? '-' }}</td>
                            <td>{{ $row->batch_number }}</td>
                            <td>{{ number_format($row->quantity, 2) }}</td>
                            <td>{{ $row->expiry_date ? $row->expiry_date->format('M d, Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; color: var(--gray-500); padding: 24px;">No {{ $type === 'expired' ? 'expired' : 'expiring soon' }} batches.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
