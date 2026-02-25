@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
    <div class="page-header animate-in" style="max-width: 1000px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-edit"></i>
            Edit Product: {{ $product->name }}
        </div>
        <div class="page-subtitle">Modify product details and pricing.</div>
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
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Category</label>
                            <select name="category_id" required
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Unit</label>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <select name="unit_id" required
                                    style="flex: 1; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->short_code }})
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('units.create') }}" class="btn btn-secondary" style="padding: 12px 16px; white-space: nowrap;" title="Add new unit">
                                    <i class="fas fa-plus"></i> Add unit
                                </a>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Product code (Optional)</label>
                            <input type="text" name="code" value="{{ old('code', $product->code) }}" placeholder="e.g. SKU-001"
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Barcode (Optional)</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="Scan or type barcode"
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Cost
                                Price ($)</label>
                            <input type="number" step="0.01" name="cost_price"
                                value="{{ old('cost_price', $product->cost_price) }}" required
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Selling
                                Price ($)</label>
                            <input type="number" step="0.01" name="selling_price"
                                value="{{ old('selling_price', $product->selling_price) }}" required
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Additional prices</label>
                        <p style="font-size: 0.85rem; color: var(--gray-500); margin-bottom: 10px;">Extra price options (e.g. Wholesale, Tier 2). Selling and cost price above are always available.</p>
                        <div id="extraPrices">
                            @php $prices = old('prices', $product->productPrices ?? []); @endphp
                            @forelse($prices as $i => $pp)
                            <div class="extra-price-row" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end; margin-bottom: 10px;">
                                <input type="text" name="prices[{{ $i }}][label]" value="{{ is_array($pp) ? ($pp['label'] ?? '') : $pp->label }}" placeholder="Label" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                                <input type="number" step="0.01" min="0" name="prices[{{ $i }}][price]" value="{{ is_array($pp) ? ($pp['price'] ?? '') : $pp->price }}" placeholder="Price" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                                <button type="button" class="btn btn-secondary remove-price" style="padding: 10px 14px;">Remove</button>
                            </div>
                            @empty
                            <div class="extra-price-row" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end; margin-bottom: 10px;">
                                <input type="text" name="prices[0][label]" placeholder="Label" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                                <input type="number" step="0.01" min="0" name="prices[0][price]" placeholder="Price" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;">
                                <button type="button" class="btn btn-secondary remove-price" style="padding: 10px 14px;">Remove</button>
                            </div>
                            @endforelse
                        </div>
                        <button type="button" id="addPriceRow" class="btn btn-secondary" style="margin-top: 8px;"><i class="fas fa-plus"></i> Add another price</button>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Item discount (optional)</label>
                        <p style="font-size: 0.85rem; color: var(--gray-500); margin-bottom: 10px;">Default discount for this product at POS: flat amount (Rs) or percentage.</p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; color: var(--gray-600); margin-bottom: 4px;">Type</label>
                                <select name="discount_type" id="editDiscountType" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                    <option value="">None</option>
                                    <option value="flat" {{ old('discount_type', $product->discount_type) === 'flat' ? 'selected' : '' }}>Flat (Rs)</option>
                                    <option value="percent" {{ old('discount_type', $product->discount_type) === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; color: var(--gray-600); margin-bottom: 4px;">Value</label>
                                <input type="number" step="0.01" min="0" name="discount_value" value="{{ old('discount_value', $product->discount_value ?? 0) }}" placeholder="0.00" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Description</label>
                        <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; resize: none;">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <script>
                document.getElementById('addPriceRow').addEventListener('click', function() {
                    const container = document.getElementById('extraPrices');
                    const n = container.querySelectorAll('.extra-price-row').length;
                    const row = document.createElement('div');
                    row.className = 'extra-price-row';
                    row.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end; margin-bottom: 10px;';
                    row.innerHTML = '<input type="text" name="prices[' + n + '][label]" placeholder="Label" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;"><input type="number" step="0.01" min="0" name="prices[' + n + '][price]" placeholder="Price" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;"><button type="button" class="btn btn-secondary remove-price" style="padding: 10px 14px;">Remove</button>';
                    container.appendChild(row);
                    row.querySelector('.remove-price').addEventListener('click', function() { row.remove(); });
                });
                document.querySelectorAll('.remove-price').forEach(btn => btn.addEventListener('click', function() { this.closest('.extra-price-row').remove(); }));
            </script>

            <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
@endsection