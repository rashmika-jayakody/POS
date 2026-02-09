@extends('layouts.admin')

@section('title', 'GRN Details: ' . $grn->grn_number)

@section('content')
    <div class="page-header animate-in">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <a href="{{ route('grns.index') }}"
                    style="color: var(--light-blue); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 6px; margin-bottom: 12px;">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <div class="page-title">
                    <i class="fas fa-file-invoice"></i>
                    {{ $grn->grn_number }}
                </div>
                <div class="page-subtitle">Goods Received Note detail and status.</div>
            </div>

            <div style="display: flex; gap: 12px;">
                @if($grn->status == 'draft')
                    <form action="{{ route('grns.receive', $grn->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="background: var(--success); border: none;">
                            <i class="fas fa-check"></i> Receive & Update Stock
                        </button>
                    </form>
                @endif
                <button class="btn btn-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div class="section animate-in">
            <h3 class="section-title"><i class="fas fa-list"></i> Items Received</h3>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="text-align: center;">Quantity</th>
                            <th style="text-align: right;">Unit Price</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grn->items as $item)
                            <tr>
                                <td>
                                    <span style="font-weight: 700; color: var(--gray-900);">{{ $item->product->name }}</span>
                                    <div style="font-size: 0.75rem; color: var(--gray-500);">{{ $item->product->barcode }}</div>
                                </td>
                                <td style="text-align: center; font-weight: 700;">{{ number_format($item->quantity, 2) }}
                                    {{ $item->product->unit->short_code }}</td>
                                <td style="text-align: right; font-family: monospace;">
                                    ${{ number_format($item->unit_price, 2) }}</td>
                                <td
                                    style="text-align: right; font-family: monospace; font-weight: 700; color: var(--navy-dark);">
                                    ${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"
                                style="text-align: right; padding: 20px; font-weight: 800; border-top: 2px solid var(--gray-100);">
                                TOTAL AMOUNT:</td>
                            <td
                                style="text-align: right; padding: 20px; font-weight: 800; border-top: 2px solid var(--gray-100); font-size: 1.2rem; color: var(--light-blue); font-family: monospace;">
                                ${{ number_format($grn->total_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div>
            <div class="section animate-in">
                <h3 class="section-title"><i class="fas fa-info-circle"></i> GRN Information</h3>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div
                            style="font-size: 0.75rem; color: var(--gray-500); font-weight: 700; text-transform: uppercase;">
                            Status</div>
                        <span
                            class="status-badge {{ $grn->status == 'received' ? 'active' : ($grn->status == 'draft' ? 'pending' : 'inactive') }}"
                            style="margin-top: 4px;">
                            <span class="status-dot"></span>
                            {{ ucfirst($grn->status) }}
                        </span>
                    </div>
                    <div>
                        <div
                            style="font-size: 0.75rem; color: var(--gray-500); font-weight: 700; text-transform: uppercase;">
                            Supplier</div>
                        <div style="font-weight: 700; color: var(--navy-dark); margin-top: 4px;">{{ $grn->supplier->name }}
                        </div>
                        <div style="font-size: 0.85rem; color: var(--gray-500);">{{ $grn->supplier->contact_person }}</div>
                    </div>
                    <div>
                        <div
                            style="font-size: 0.75rem; color: var(--gray-500); font-weight: 700; text-transform: uppercase;">
                            Incoming Branch</div>
                        <div style="font-weight: 700; color: var(--navy-dark); margin-top: 4px;">{{ $grn->branch->name }}
                        </div>
                    </div>
                    <div>
                        <div
                            style="font-size: 0.75rem; color: var(--gray-500); font-weight: 700; text-transform: uppercase;">
                            Received By</div>
                        <div style="font-weight: 700; color: var(--navy-dark); margin-top: 4px;">{{ $grn->user->name }}
                        </div>
                        <div style="font-size: 0.85rem; color: var(--gray-500);">{{ $grn->received_date }}</div>
                    </div>
                </div>
            </div>

            <div class="section animate-in">
                <h3 class="section-title"><i class="fas fa-sticky-note"></i> Notes</h3>
                <p style="color: var(--gray-600); font-size: 0.9rem; line-height: 1.6;">
                    {{ $grn->notes ?: 'No additional notes provided for this shipment.' }}
                </p>
            </div>
        </div>
    </div>
@endsection