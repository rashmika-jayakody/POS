@extends('layouts.admin')

@section('title', __('Goods Received Notes (GRN)'))

@section('content')
    <div class="page-header animate-in">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="page-title">
                    <i class="fas fa-file-invoice"></i>
                    {{ __('Goods Received Notes') }}
                </div>
                <div class="page-subtitle">{{ __('Track incoming inventory and update stock levels.') }}</div>
            </div>
            <a href="{{ route('grns.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                {{ __('New GRN') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div
            style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('GRN Number') }}</th>
                        <th>{{ __('Supplier') }}</th>
                        <th>{{ __('Branch') }}</th>
                        <th>{{ __('Received Date') }}</th>
                        <th>{{ __('Total Amount') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th style="text-align: right;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grns as $grn)
                        <tr>
                            <td>
                                <span style="font-weight: 700; color: var(--navy-dark);">{{ $grn->grn_number }}</span>
                            </td>
                            <td>{{ $grn->supplier->name }}</td>
                            <td>{{ $grn->branch->name }}</td>
                            <td>{{ $grn->received_date }}</td>
                            <td style="font-family: monospace; font-weight: 700; color: var(--gray-900);">
                                {{ $currencySymbol ?? 'Rs' }}{{ number_format($grn->total_amount, 2) }}</td>
                            <td>
                                <span
                                    class="status-badge {{ $grn->status == 'received' ? 'active' : ($grn->status == 'draft' ? 'pending' : 'inactive') }}">
                                    <span class="status-dot"></span>
                                    {{ __(ucfirst($grn->status)) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('grns.show', $grn->id) }}" class="btn btn-secondary"
                                        style="padding: 6px 10px; font-size: 0.75rem;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($grn->status == 'draft')
                                        <form action="{{ route('grns.receive', $grn->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary"
                                                style="padding: 6px 10px; font-size: 0.75rem; background: var(--success); box-shadow: none;">
                                                <i class="fas fa-check"></i> {{ __('Receive') }}
                                            </button>
                                        </form>
                                        <form action="{{ route('grns.destroy', $grn->id) }}" method="POST"
                                            onsubmit="return confirm('{{ __('Delete this GRN?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn"
                                                style="padding: 6px 10px; font-size: 0.75rem; background: rgba(255, 107, 130, 0.1); color: var(--accent-coral); border: 1px solid rgba(255, 107, 130, 0.2);">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection