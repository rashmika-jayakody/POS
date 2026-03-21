@extends('layouts.admin')

@section('title', __('Edit Product'))

@section('content')
    <div class="page-header animate-in" style="max-width: 1000px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-edit"></i>
            {{ __('Edit Product: :name', ['name' => $product->name]) }}
        </div>
        <div class="page-subtitle">{{ __('Modify product details and pricing.') }}</div>
    </div>

    <div class="section animate-in" style="max-width: 1000px; margin: 0 auto;">
        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <!-- Basic Info -->
                <div>
                    <div style="margin-bottom: 20px;">
                        <label
                            style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Product
                            Name</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                            style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Category') }}</label>
                            <select name="category_id" required
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Unit') }}</label>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <select name="unit_id" required
                                    style="flex: 1; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->short_code }})
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('units.create') }}" class="btn btn-secondary" style="padding: 12px 16px; white-space: nowrap;" title="{{ __('Add new unit') }}">
                                    <i class="fas fa-plus"></i> {{ __('Add unit') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Product code (Optional)') }}</label>
                            <input type="text" name="code" value="{{ old('code', $product->code) }}" placeholder="{{ __('e.g. SKU-001') }}"
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Barcode (Optional)') }}</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="{{ __('Scan or type barcode') }}"
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Cost Price') }} ({{ $currencySymbol ?? 'Rs' }}) <span style="font-size: 0.75rem; color: var(--gray-500); font-weight: 400;">({{ __('Read-only, FIFO from GRN') }})</span></label>
                            <input type="number" step="0.01" name="cost_price"
                                value="{{ old('cost_price', $fifoCostPrice ?? $product->cost_price) }}" required readonly
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; background-color: #f8f9fa; cursor: not-allowed;"
                                id="cost_price_input"
                                title="{{ __('Cost price shows the FIFO cost (first batch cost that will be sold next). This field is read-only and updates automatically from GRN batches.') }}">
                            <p style="font-size: 0.75rem; color: var(--gray-500); margin-top: 4px;">
                                <i class="fas fa-info-circle"></i> {{ __('FIFO cost price (first batch with stock):') }} {{ $currencySymbol ?? 'Rs' }} {{ number_format($fifoCostPrice ?? 0, 2) }}
                            </p>
                        </div>
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Selling Price') }} ({{ $currencySymbol ?? 'Rs' }})</label>
                            <input type="number" step="0.01" name="selling_price"
                                value="{{ old('selling_price', $product->selling_price) }}" required
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Price levels (for POS)') }}</label>
                        <p style="font-size: 0.85rem; color: var(--gray-500); margin-bottom: 10px;">{{ __('Add multiple price levels (e.g. Retail, Wholesale). At POS, the cashier will see a popup to select which price to use for each item. Selling price above is always included as the first option.') }}</p>
                        <div id="extraPrices">
                            @php $prices = old('prices', $product->productPrices ?? []); @endphp
                            @forelse($prices as $i => $pp)
                            <div class="extra-price-row" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end; margin-bottom: 10px;">
                                <input type="text" name="prices[{{ $i }}][label]" value="{{ is_array($pp) ? ($pp['label'] ?? '') : $pp->label }}" placeholder="{{ __('Label e.g. Wholesale') }}" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                                <input type="number" step="0.01" min="0" name="prices[{{ $i }}][price]" value="{{ is_array($pp) ? ($pp['price'] ?? '') : $pp->price }}" placeholder="{{ __('Price') }}" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                                <button type="button" class="btn btn-secondary remove-price" style="padding: 10px 14px;">{{ __('Remove') }}</button>
                            </div>
                            @empty
                            <div class="extra-price-row" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end; margin-bottom: 10px;">
                                <input type="text" name="prices[0][label]" placeholder="{{ __('Label e.g. Wholesale') }}" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                                <input type="number" step="0.01" min="0" name="prices[0][price]" placeholder="{{ __('Price') }}" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                                <button type="button" class="btn btn-secondary remove-price" style="padding: 10px 14px;">{{ __('Remove') }}</button>
                            </div>
                            @endforelse
                        </div>
                        <button type="button" id="addPriceRow" class="btn btn-secondary" style="margin-top: 8px;"><i class="fas fa-plus"></i> {{ __('Add another price') }}</button>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Item discount (optional)') }}</label>
                        <p style="font-size: 0.85rem; color: var(--gray-500); margin-bottom: 10px;">{{ __('Default discount for this product at POS: flat amount or percentage.') }}</p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; color: var(--gray-600); margin-bottom: 4px;">{{ __('Type') }}</label>
                                <select name="discount_type" id="editDiscountType" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                    <option value="">{{ __('None') }}</option>
                                    <option value="flat" {{ old('discount_type', $product->discount_type) === 'flat' ? 'selected' : '' }}>{{ __('Flat (Rs)') }}</option>
                                    <option value="percent" {{ old('discount_type', $product->discount_type) === 'percent' ? 'selected' : '' }}>{{ __('Percentage (%)') }}</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; color: var(--gray-600); margin-bottom: 4px;">{{ __('Value') }}</label>
                                <input type="number" step="0.01" min="0" name="discount_value" value="{{ old('discount_value', $product->discount_value ?? 0) }}" placeholder="0.00" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Description') }}</label>
                        <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; resize: none;">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <script>
                window.__productT = { labelPlaceholder: @json(__('Label e.g. Wholesale')), pricePlaceholder: @json(__('Price')), remove: @json(__('Remove')) };
                document.getElementById('addPriceRow').addEventListener('click', function() {
                    const container = document.getElementById('extraPrices');
                    const n = container.querySelectorAll('.extra-price-row').length;
                    const row = document.createElement('div');
                    row.className = 'extra-price-row';
                    row.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end; margin-bottom: 10px;';
                    var t = window.__productT || { labelPlaceholder: 'Label', pricePlaceholder: 'Price', remove: 'Remove' };
                    row.innerHTML = '<input type="text" name="prices[' + n + '][label]" placeholder="' + t.labelPlaceholder + '" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;"><input type="number" step="0.01" min="0" name="prices[' + n + '][price]" placeholder="' + t.pricePlaceholder + '" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;"><button type="button" class="btn btn-secondary remove-price" style="padding: 10px 14px;">' + t.remove + '</button>';
                    container.appendChild(row);
                    row.querySelector('.remove-price').addEventListener('click', function() { row.remove(); });
                });
                document.querySelectorAll('.remove-price').forEach(btn => btn.addEventListener('click', function() { this.closest('.extra-price-row').remove(); }));
            </script>

            <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Update Product') }}</button>
            </div>
        </form>
    </div>
@endsection