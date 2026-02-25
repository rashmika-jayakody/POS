@extends('layouts.admin')

@section('title', 'New Goods Received Note')

@section('content')
    <div class="page-header animate-in" style="max-width: 1200px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-plus-circle"></i>
            Create New GRN
        </div>
        <div class="page-subtitle">Record incoming goods and prepare to update inventory.</div>
    </div>

    <div style="max-width: 1200px; margin: 0 auto;">
        <form action="{{ route('grns.store') }}" method="POST" id="grn-form">
            @csrf
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 24px;">
                <!-- GRN Header -->
                <div class="section" style="margin-bottom: 0;">
                    <h3 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h3>

                    <div style="margin-bottom: 16px;">
                        <label
                            style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Supplier</label>
                        <select name="supplier_id" required
                            style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Branch
                            (Recipient)</label>
                        <select name="branch_id" required
                            style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label
                            style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Received
                            Date</label>
                        <input type="date" name="received_date" value="{{ date('Y-m-d') }}" required
                            style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    </div>
                </div>

                <div class="section" style="margin-bottom: 0;">
                    <h3 class="section-title"><i class="fas fa-sticky-note"></i> Additional Details</h3>
                    <label
                        style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Notes</label>
                    <textarea name="notes" rows="6" placeholder="Any specific details about this delivery..."
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; resize: none;"></textarea>
                </div>
            </div>

            <!-- GRN Items -->
            <div class="section animate-in">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 class="section-title" style="margin-bottom: 0;"><i class="fas fa-list-ul"></i> Received Items</h3>
                    <button type="button" id="add-item" class="btn btn-secondary" style="padding: 8px 16px;">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>

                <div class="table-wrapper">
                    <table class="table" id="items-table">
                        <thead>
                            <tr>
                                <th style="width: 28%;">Product</th>
                                <th style="width: 10%;">Quantity</th>
                                <th style="width: 14%;">Unit Cost ({{ $currencySymbol ?? 'Rs' }})</th>
                                <th style="width: 14%;">Batch / Lot #</th>
                                <th style="width: 12%;">Expiry Date</th>
                                <th style="width: 12%;">Subtotal</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <!-- Items injected here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="text-align: right; font-weight: 800; padding: 20px;">TOTAL AMOUNT:
                                </td>
                                <td id="total-amount"
                                    style="font-weight: 800; padding: 20px; font-size: 1.2rem; color: var(--light-blue); font-family: monospace;">
                                    {{ $currencySymbol ?? 'Rs' }}0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid var(--gray-100);">
                    <a href="{{ route('grns.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save GRN
                    </button>
                </div>
            </div>
            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const tableBody = document.getElementById('items-body');
                    const addItemBtn = document.getElementById('add-item');
                    const totalDisplay = document.getElementById('total-amount');
                    let itemIndex = 0;

                    const products = @json($products);
                    const currencySymbol = @json($currencySymbol ?? 'Rs');

                    function calculateTotals() {
                        let grandTotal = 0;
                        document.querySelectorAll('.subtotal-input').forEach(input => {
                            grandTotal += parseFloat(input.value || 0);
                        });
                        totalDisplay.textContent = currencySymbol + grandTotal.toFixed(2);
                    }

                    function addItemRow() {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                                    <td>
                                        <select name="items[${itemIndex}][product_id]" class="product-select" required style="width: 100%; padding: 8px; border: 1px solid var(--gray-300); border-radius: 6px;">
                                            <option value="">Select Product</option>
                                            ${products.map(p => `<option value="${p.id}" data-cost="${p.cost_price}">${p.name}</option>`).join('')}
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="quantity-input" required min="0.01" value="1" style="width: 100%; padding: 8px; border: 1px solid var(--gray-300); border-radius: 6px;">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="price-input" required min="0" value="0.00" style="width: 100%; padding: 8px; border: 1px solid var(--gray-300); border-radius: 6px;">
                                    </td>
                                    <td>
                                        <input type="text" name="items[${itemIndex}][batch_number]" placeholder="Optional" maxlength="100" style="width: 100%; padding: 8px; border: 1px solid var(--gray-300); border-radius: 6px;">
                                    </td>
                                    <td>
                                        <input type="date" name="items[${itemIndex}][expiry_date]" style="width: 100%; padding: 8px; border: 1px solid var(--gray-300); border-radius: 6px;">
                                    </td>
                                    <td style="font-family: monospace; font-weight: 700;">
                                        <span class="subtotal-prefix">{{ $currencySymbol ?? 'Rs' }}</span><input type="number" class="subtotal-input" readonly value="0.00" style="border: none; background: transparent; width: 80px; font-weight: 700; color: var(--navy-dark); outline: none;">
                                    </td>
                                    <td>
                                        <button type="button" class="remove-item" style="color: var(--danger); background: none; border: none; cursor: pointer;"><i class="fas fa-times"></i></button>
                                    </td>
                                `;

                        tableBody.appendChild(row);

                        const productSelect = row.querySelector('.product-select');
                        const quantityInput = row.querySelector('.quantity-input');
                        const priceInput = row.querySelector('.price-input');
                        const subtotalInput = row.querySelector('.subtotal-input');
                        const removeBtn = row.querySelector('.remove-item');

                        function updateRowSubtotal() {
                            const q = parseFloat(quantityInput.value || 0);
                            const p = parseFloat(priceInput.value || 0);
                            subtotalInput.value = (q * p).toFixed(2);
                            calculateTotals();
                        }

                        productSelect.addEventListener('change', function () {
                            const selectedOption = this.options[this.selectedIndex];
                            const cost = selectedOption.dataset.cost;
                            if (cost) priceInput.value = cost;
                            updateRowSubtotal();
                        });

                        quantityInput.addEventListener('input', updateRowSubtotal);
                        priceInput.addEventListener('input', updateRowSubtotal);
                        removeBtn.addEventListener('click', function () {
                            row.remove();
                            calculateTotals();
                        });

                        itemIndex++;
                    }

                    addItemBtn.addEventListener('click', addItemRow);

                    // Add first row on load
                    addItemRow();
                });
            </script>
            @endpush
        </form>
    </div>
@endsection