@extends('layouts.admin')

@section('title', __('Products & Inventory'))

@section('content')
    <div class="page-header animate-in">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="page-title">
                    <i class="fas fa-box"></i>
                    {{ __('Product Management') }}
                </div>
                <div class="page-subtitle">
                    @if(($posType ?? 'retail') === 'restaurant')
                        {{ __('Manage your menu items, pricing, and modifiers.') }}
                    @else
                        {{ __('Manage your product catalog, pricing, and view real-time stock levels.') }}
                    @endif
                </div>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                {{ __('Add New Product') }}
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
                        <th>{{ __('Product Information') }}</th>
                        <th>{{ __('Pricing') }}</th>
                        @if(($posType ?? 'retail') === 'retail')
                        <th>{{ __('Stock Levels') }}</th>
                        @endif
                        <th>{{ __('Status') }}</th>
                        <th style="text-align: right;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>
                                <span
                                    style="font-weight: 700; color: var(--gray-900); display: block; margin-bottom: 4px;">{{ $product->name }}</span>
                                <div
                                    style="font-size: 0.8rem; color: var(--gray-500); display: flex; align-items: center; gap: 8px;">
                                    @if($product->code)<span><i class="fas fa-hashtag"></i> {{ $product->code }}</span>@endif
                                    <span><i class="fas fa-barcode"></i> {{ $product->barcode ?? __('No Barcode') }}</span>
                                    <span
                                        style="background: rgba(74, 158, 255, 0.1); color: var(--light-blue); padding: 2px 8px; border-radius: 4px; font-weight: 700;">{{ $product->category->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.75rem; color: var(--gray-500); margin-bottom: 2px;">{{ __('Selling Price') }}:
                                </div>
                                <span
                                    style="font-family: monospace; font-weight: 700; color: var(--accent-teal); font-size: 1.1rem;">{{ $currencySymbol ?? 'Rs' }}{{ number_format($product->selling_price, 2) }}</span>
                                <div style="font-size: 0.7rem; color: var(--gray-500); margin-top: 2px;">{{ __('Cost') }}:
                                    {{ $currencySymbol ?? 'Rs' }}{{ number_format($product->cost_price, 2) }}</div>
                            </td>
                            @if(($posType ?? 'retail') === 'retail')
                            <td>
                                <div
                                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px;">
                                    @foreach($product->stocks as $stock)
                                        <div
                                            style="padding: 8px; background: var(--gray-100); border-radius: 8px; text-align: center; border: 1px solid rgba(74, 158, 255, 0.05);">
                                            <span
                                                style="display: block; font-size: 0.65rem; color: var(--gray-500); font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">{{ $stock->branch->name }}</span>
                                            <span
                                                style="font-size: 1.1rem; font-weight: 800; color: {{ $stock->quantity <= $stock->low_stock_threshold ? 'var(--accent-coral)' : 'var(--navy-dark)' }}">
                                                {{ number_format($stock->quantity, 0) }}
                                            </span>
                                            <span style="font-size: 0.65rem; color: var(--gray-500);">/
                                                {{ $product->unit->short_code }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            @endif
                            <td>
                                @if(($posType ?? 'retail') === 'restaurant')
                                    <span class="status-badge {{ $product->is_active ? 'active' : 'inactive' }}">
                                        <span class="status-dot"></span>
                                        {{ $product->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                @else
                                    @php $totalStock = $product->stocks->sum('quantity'); @endphp
                                    <span class="status-badge {{ $totalStock > 0 ? 'active' : 'inactive' }}">
                                        <span class="status-dot"></span>
                                        {{ $totalStock > 0 ? __('In Stock') : __('Out of Stock') }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-secondary"
                                        style="padding: 6px 10px; font-size: 0.75rem;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                        onsubmit="return confirm('{{ __('Delete this product?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn"
                                            style="padding: 6px 10px; font-size: 0.75rem; background: rgba(255, 107, 130, 0.1); color: var(--accent-coral); border: 1px solid rgba(255, 107, 130, 0.2);">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection