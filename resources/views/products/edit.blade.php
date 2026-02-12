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
                            <select name="unit_id" required
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->short_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label
                            style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Barcode
                            (Optional)</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                            placeholder="Scan or type barcode"
                            style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
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
                        <label
                            style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Description</label>
                        <textarea name="description" rows="4"
                            style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; resize: none;">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
@endsection