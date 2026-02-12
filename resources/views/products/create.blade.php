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
                            <select name="unit_id" required
                                style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label
                            style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Barcode
                            (Optional)</label>
                        <input type="text" name="barcode" placeholder="Scan or type barcode"
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
                        <label
                            style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Description</label>
                        <textarea name="description" rows="4"
                            style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; resize: none;"></textarea>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Product</button>
            </div>
        </form>
    </div>
@endsection