@extends('layouts.admin')

@section('title', 'Add New Product')

@section('content')
    <div class="page-header animate-in" style="max-width: 1000px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-plus-circle"></i>
            Add New Product
        </div>
        <div class="page-subtitle">Create a new item in your product catalog.</div>
    </div>

    <div class="section animate-in" style="max-width: 1000px; margin: 0 auto;">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <!-- Basic Info -->
                <div>
                    <div style="margin-bottom: 20px;">
                        <label
                            style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Product
                            Name</label>
                        <input type="text" name="name" required placeholder="e.g., Organic Carrots"
                            style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Category</label>
                            <select name="category_id" required
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Unit</label>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <select name="unit_id" required
                                    style="flex: 1; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                    @forelse($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_code }})</option>
                                    @empty
                                        <option value="">— No units yet —</option>
                                    @endforelse
                                </select>
                                <a href="{{ route('units.create', ['return' => 'products.create']) }}" class="btn btn-secondary" style="padding: 12px 16px; white-space: nowrap;" title="Add new unit">
                                    <i class="fas fa-plus"></i> Add unit
                                </a>
                            </div>
                            @if($units->isEmpty())
                                <p style="font-size: 0.8rem; color: var(--gray-500); margin-top: 6px;">Add at least one unit (e.g. kg, pcs) to create products.</p>
                            @endif
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Product code (Optional)</label>
                            <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. SKU-001"
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                            <p style="font-size: 0.8rem; color: var(--gray-500); margin-top: 4px;">Internal code, different from barcode.</p>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Barcode (Optional)</label>
                            <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Scan or type barcode"
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
                            <input type="number" step="0.01" name="cost_price" required placeholder="0.00"
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                        <div>
                            <label
                                style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Selling
                                Price ($)</label>
                            <input type="number" step="0.01" name="selling_price" required placeholder="0.00"
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Additional prices (optional)</label>
                        <p style="font-size: 0.85rem; color: var(--gray-500); margin-bottom: 10px;">Add more price options (e.g. Wholesale, Tier 2) for this product. Selling and cost price above are always available.</p>
                        <div id="extraPrices"></div>
                        <button type="button" id="addPriceRow" class="btn btn-secondary" style="margin-top: 8px;"><i class="fas fa-plus"></i> Add another price</button>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Description</label>
                        <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; resize: none;"></textarea>
                    </div>
                </div>
            </div>
            <script>
                function addPriceRow() {
                    const container = document.getElementById('extraPrices');
                    const n = container.querySelectorAll('.extra-price-row').length;
                    const row = document.createElement('div');
                    row.className = 'extra-price-row';
                    row.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end; margin-bottom: 10px;';
                    row.innerHTML = '<input type="text" name="prices[' + n + '][label]" placeholder="Label e.g. Wholesale" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;"><input type="number" step="0.01" min="0" name="prices[' + n + '][price]" placeholder="Price" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px;"><button type="button" class="btn btn-secondary remove-price" style="padding: 10px 14px;">Remove</button>';
                    container.appendChild(row);
                    row.querySelector('.remove-price').addEventListener('click', function() { row.remove(); });
                }
                document.getElementById('addPriceRow').addEventListener('click', addPriceRow);
            </script>

            <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Product</button>
            </div>
        </form>
    </div>
@endsection