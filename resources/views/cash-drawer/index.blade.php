<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Terminal - Cash Drawer</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif; background: #e8e8e8; font-size: 13px; min-height: 100vh; padding: 10px; }
        .pos-container { max-width: 1400px; height: calc(100vh - 20px); margin: auto; background: #fff; display: flex; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
        .left { flex: 3; border-right: 1px solid #e0e0e0; display: flex; flex-direction: column; background: white; min-width: 0; }
        .right { flex: 1.2; background: white; display: flex; flex-direction: column; border-left: 1px solid #e0e0e0; min-width: 280px; }

        .top-info { background: #f5f5f5; padding: 12px; border-bottom: 1px solid #ddd; display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; }
        .top-info input { padding: 8px 12px; border: 1px solid #ccc; font-size: 12px; border-radius: 4px; background: white; }
        .top-info input[readonly] { background: #eee; }

        .product-search-section { padding: 10px 12px; background: #e8f4fc; border-bottom: 1px solid #ddd; position: relative; }
        .product-search-section input { width: 100%; padding: 10px 12px; border: 1px solid #4a9eff; border-radius: 6px; font-size: 14px; }
        .product-dropdown { position: absolute; left: 12px; right: 12px; top: 100%; margin-top: 4px; max-height: 220px; overflow-y: auto; background: #fff; border: 1px solid #4a9eff; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 100; display: none; }
        .product-dropdown.show { display: block; }
        .product-dropdown .product-item { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
        .product-dropdown .product-item:hover { background: #e8f4fc; }
        .product-dropdown .product-item:last-child { border-bottom: none; }
        .product-dropdown .product-item .name { font-weight: 600; color: #333; }
        .product-dropdown .product-item .price { color: #16a34a; font-weight: 700; }
        .product-dropdown .product-item .code { font-size: 11px; color: #666; margin-left: 8px; }
        .product-dropdown .no-results { padding: 12px; color: #999; text-align: center; }

        .table-header, .table-row { display: grid; grid-template-columns: 40px 60px 1.5fr 80px 70px 60px 80px 44px; gap: 4px; padding: 8px 12px; align-items: center; border-bottom: 1px solid #f0f0f0; font-size: 12px; }
        .table-header { background: #333; color: #fff; font-weight: bold; text-transform: uppercase; position: sticky; top: 0; z-index: 1; }
        .table-row { background: white; }
        .table-row:hover { background: #f9f9f9; }
        .table-scroll { overflow-y: auto; flex: 1; min-height: 120px; }
        .table-delete-btn { background: #ff6b6b; color: white; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer; font-size: 11px; }
        .table-delete-btn:hover { background: #ff5252; }

        .numpad-section { padding: 10px 12px; background: #f9f9f9; border-top: 1px solid #ddd; }
        .numpad-label { font-weight: bold; color: #333; margin-bottom: 6px; font-size: 11px; }
        .numpad-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
        .numpad-btn { padding: 14px; border: 1px solid #333; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 20px; background: #000; color: white; }
        .numpad-btn:hover { background: #222; }
        .numpad-btn.clear { background: #ff6b6b; border-color: #ff6b6b; font-size: 16px; }

        .quick-links-section { padding: 10px 12px; background: #f0f0f0; border-top: 1px solid #ddd; }
        .quick-links-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
        .quick-link-btn { padding: 10px; border: 1px solid #999; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 11px; background: white; color: #333; text-transform: uppercase; }
        .quick-link-btn:hover { background: #333; color: white; }
        .quick-link-btn.blue { background: #4a90e2; color: white; border-color: #4a90e2; }
        .quick-link-btn.red { background: #ff6b6b; color: white; border-color: #ff6b6b; }
        kbd { font-size: 10px; opacity: 0.9; margin-left: 4px; }

        .panel-title { background: #333; color: white; padding: 10px 12px; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .payment-row { display: flex; justify-content: space-between; padding: 10px 12px; border-bottom: 1px solid #f0f0f0; align-items: center; }
        .payment-value { font-size: 15px; font-weight: bold; color: #333; }
        .payment-value.total { font-size: 20px; color: #16a34a; }
        .pay-btn { background: #16a34a; color: white; font-size: 16px; padding: 14px; border: none; width: 100%; cursor: pointer; font-weight: bold; margin-top: auto; text-transform: uppercase; }
        .pay-btn:hover { background: #15803d; }
        .payment-content { flex: 1; overflow-y: auto; }

        .loyalty-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .loyalty-modal.show { display: flex; }
        .loyalty-modal-content { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); max-width: 400px; width: 90%; }
        .loyalty-modal-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 16px; }
        .loyalty-modal-btn { padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .loyalty-modal-btn.confirm { background: #333; color: white; }
        .loyalty-modal-btn.skip { background: #f0f0f0; color: #333; border: 1px solid #ccc; }

        /* Price selection modal */
        .price-modal { display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .price-modal.show { display: flex; }
        .price-modal-content { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); min-width: 340px; max-width: 95%; }
        .price-modal-content h2 { margin: 0 0 4px 0; font-size: 18px; color: #333; }
        .price-modal-subtitle { margin: 0 0 4px 0; font-size: 13px; color: #666; }
        .price-modal-hint { margin: 0 0 12px 0; font-size: 12px; color: #888; }
        .price-option { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; margin-bottom: 8px; border: 2px solid #e0e0e0; border-radius: 6px; cursor: pointer; transition: all 0.2s; }
        .price-option:hover { border-color: #4a9eff; background: #f0f8ff; }
        .price-option.selected { border-color: #4a9eff; background: #e8f4fc; }
        .price-option .label { font-weight: 600; color: #333; }
        .price-option .amount { font-weight: 700; color: #16a34a; }
        .price-modal-custom { margin: 12px 0; }
        .price-modal-custom label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; }
        .price-modal-custom input { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .price-modal-qty { margin: 12px 0; }
        .price-modal-qty label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; }
        .price-modal-qty input { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .price-modal-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; }
        .price-modal-btn { padding: 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px; }
        .price-modal-btn.add { background: #16a34a; color: white; }
        .price-modal-btn.add:hover { background: #15803d; }
        .price-modal-btn.cancel { background: #f0f0f0; color: #333; border: 1px solid #ccc; }

        /* Quantity modal */
        .qty-modal { display: none; position: fixed; z-index: 1002; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .qty-modal.show { display: flex; }
        .qty-modal-content { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); min-width: 320px; max-width: 95%; }
        .qty-modal-content h2 { margin: 0 0 8px 0; font-size: 18px; color: #333; }
        .qty-modal-summary { font-size: 14px; color: #666; margin-bottom: 4px; }
        .qty-modal-hint { font-size: 12px; color: #888; margin-bottom: 12px; }
        .qty-modal-qty { margin: 12px 0; }
        .qty-modal-qty label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; }
        .qty-modal-qty input { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .qty-modal-buttons { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-top: 20px; }
        .qty-modal-btn { padding: 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px; }
        .qty-modal-btn.add { background: #16a34a; color: white; }
        .qty-modal-btn.add:hover { background: #15803d; }
        .qty-modal-btn.cancel { background: #f0f0f0; color: #333; border: 1px solid #ccc; }
        .qty-modal-btn.back { background: #e0e0e0; color: #333; border: 1px solid #ccc; }
        .qty-modal-btn.back:hover { background: #d0d0d0; }

        /* Print receipt - hidden on screen, shown when printing */
        #receipt-print { display: none; }
        @media print {
            body * { visibility: hidden; }
            #receipt-print, #receipt-print * { visibility: visible; }
            #receipt-print { display: block !important; position: absolute; left: 0; top: 0; width: 100%; padding: 20px; font-size: 14px; }
            .no-print { display: none !important; }
        }
        .receipt-paper { max-width: 320px; margin: 0 auto; font-family: monospace; }
        .receipt-paper h2 { text-align: center; margin: 0 0 8px 0; font-size: 18px; }
        .receipt-paper .meta { text-align: center; font-size: 12px; color: #666; margin-bottom: 16px; }
        .receipt-paper table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 13px; }
        .receipt-paper th { text-align: left; border-bottom: 1px dashed #333; padding: 4px 0; }
        .receipt-paper td { padding: 4px 0; border-bottom: 1px dashed #ccc; }
        .receipt-paper .total-row { font-weight: bold; font-size: 16px; border-top: 2px solid #333; padding-top: 8px; margin-top: 8px; }
        .receipt-paper .thanks { text-align: center; margin-top: 20px; font-size: 14px; }
    </style>
</head>
<body>

<div id="loyaltyModal" class="loyalty-modal">
    <div class="loyalty-modal-content">
        <h2>Loyalty Customer</h2>
        <p>Enter customer phone (optional)</p>
        <input type="text" id="loyaltyPhone" placeholder="Phone number" style="width:100%;padding:10px;margin:8px 0;">
        <div class="loyalty-modal-buttons">
            <button class="loyalty-modal-btn confirm" onclick="document.getElementById('loyaltyModal').classList.remove('show')">OK</button>
            <button class="loyalty-modal-btn skip" onclick="document.getElementById('loyaltyModal').classList.remove('show')">Skip</button>
        </div>
    </div>
</div>

<div id="priceModal" class="price-modal">
    <div class="price-modal-content">
        <h2>Select selling price</h2>
        <p class="price-modal-subtitle">Product: <strong id="priceModalProductName">—</strong></p>
        <p class="price-modal-hint">This product has multiple selling prices. Select one — then you will enter quantity in the next step.</p>
        <div id="priceModalOptions"></div>
        <div class="price-option" data-price-type="custom" id="priceOptCustom">
            <span class="label">Custom price</span>
            <span class="amount">—</span>
        </div>
        <div class="price-modal-custom" id="priceModalCustomWrap" style="display:none;">
            <label>Enter amount</label>
            <input type="number" step="0.01" min="0" id="priceModalCustomInput" placeholder="0.00">
        </div>
        <div class="price-modal-buttons">
            <button type="button" class="price-modal-btn cancel" onclick="closePriceModal()">Cancel (Esc)</button>
            <button type="button" class="price-modal-btn add" onclick="confirmPriceAndOpenQtyModal()">Next: enter quantity (Enter)</button>
        </div>
        <p style="font-size: 11px; color: #999; margin-top: 12px;">↑↓ change price · 1-9 select by number</p>
    </div>
</div>

<div id="qtyModal" class="qty-modal">
    <div class="qty-modal-content">
        <h2>Enter quantity</h2>
        <p class="qty-modal-hint" id="qtyModalProductLine">—</p>
        <p class="qty-modal-summary" id="qtyModalSummary">—</p>
        <div class="qty-modal-qty">
            <label>Quantity</label>
            <input type="number" step="0.01" min="0.01" id="qtyModalQty" value="1">
        </div>
        <div class="qty-modal-buttons">
            <button type="button" class="qty-modal-btn cancel" onclick="closeQtyModal()">Cancel (Esc)</button>
            <button type="button" class="qty-modal-btn back" onclick="qtyModalBackToPrice()">Back</button>
            <button type="button" class="qty-modal-btn add" onclick="confirmQtyAndAddToCart()">Add to cart (Enter)</button>
        </div>
    </div>
</div>

<!-- Receipt content for printing (hidden until print) -->
<div id="receipt-print"></div>

<div class="pos-container no-print">
    <div class="left">
        <div class="top-info">
            <input type="text" id="invoiceNo" value="{{ $invoiceNo }}" readonly placeholder="Invoice No">
            <input type="text" id="customerName" placeholder="Customer Name">
            <input type="text" id="customerPhone" placeholder="Phone">
            <input type="text" id="dateField" readonly placeholder="Date">
            <input type="text" id="userField" value="{{ auth()->user()->name ?? 'User' }}" readonly placeholder="User">
            <input type="text" id="timeField" readonly placeholder="Time">
        </div>

        <div class="product-search-section" id="searchSection">
            <input type="text" id="productSearch" placeholder="Search by name, code or barcode..." autocomplete="off">
            <div class="product-dropdown" id="productDropdown"></div>
        </div>

        <div class="table-header">
            <div>#</div>
            <div>Code</div>
            <div>Item</div>
            <div>Price</div>
            <div>Qty</div>
            <div>Unit</div>
            <div>Total</div>
            <div></div>
        </div>
        <div class="table-scroll" id="cartBody"></div>

        <div class="numpad-section">
            <div class="numpad-label">Qty keypad (for selected / next add)</div>
            <div class="numpad-grid">
                <button type="button" class="numpad-btn" onclick="numpadInput('1')">1</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('2')">2</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('3')">3</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('4')">4</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('5')">5</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('6')">6</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('7')">7</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('8')">8</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('9')">9</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('0')">0</button>
                <button type="button" class="numpad-btn" onclick="numpadInput('.')">.</button>
                <button type="button" class="numpad-btn clear" onclick="numpadClear()">CLR</button>
            </div>
        </div>

        <div class="quick-links-section">
            <div class="quick-links-grid">
                <button type="button" class="quick-link-btn blue" onclick="document.getElementById('loyaltyModal').classList.add('show')">Customer</button>
                <button type="button" class="quick-link-btn blue" onclick="printBill()">Print Bill <kbd>Ctrl+P</kbd></button>
                <button type="button" class="quick-link-btn red" onclick="clearBill()">Clear Bill <kbd>Ctrl+Shift+C</kbd></button>
            </div>
            <p style="margin: 8px 0 0 0; font-size: 11px; color: var(--gray-500);">F2 Search | Enter Add | Esc Close | ↑↓ Select price | 1-9 Price #</p>
        </div>
    </div>

    <div class="right">
        <div class="payment-content">
            <div class="panel-title">Bill Summary</div>
            <div class="payment-row">
                <span>Subtotal:</span>
                <span class="payment-value" id="subtotalDisplay">0.00</span>
            </div>
            <div class="payment-row">
                <span>Discount:</span>
                <span class="payment-value" id="discountDisplay">0.00</span>
            </div>
            <div class="payment-row" style="background:#f5f5f5; padding: 12px;">
                <span>Total:</span>
                <span class="payment-value total" id="totalDisplay">0.00</span>
            </div>
        </div>
        <button type="button" class="pay-btn" onclick="printBill()">Print Bill</button>
    </div>
</div>

<script>
    const products = @json($productsJson);
    const storeName = @json($storeName);

    let cart = [];
    let nextQty = 1;
    let discountAmount = 0;

    function getProductList(filter) {
        const f = (filter || '').toLowerCase().trim();
        if (!f) return products;
        return products.filter(p =>
            (p.name && p.name.toLowerCase().includes(f)) ||
            (p.code && p.code.toString().toLowerCase().includes(f)) ||
            (p.barcode && p.barcode.toString().toLowerCase().includes(f))
        );
    }

    function showDropdown(items) {
        const dropdown = document.getElementById('productDropdown');
        if (!items || items.length === 0) {
            dropdown.innerHTML = '<div class="no-results">No products found</div>';
            dropdown.classList.add('show');
            return;
        }
        const list = items.slice(0, 15).map(p => {
            const name = (p.name || 'Product').replace(/"/g, '&quot;');
            const code = (p.code || '').replace(/"/g, '&quot;');
            const barcode = (p.barcode || '').replace(/"/g, '&quot;');
            const unit = (p.unit || '').replace(/"/g, '&quot;');
            const pricesJson = JSON.stringify(p.prices || [{ label: 'Selling price', price: p.price }]);
            return `<div class="product-item" data-id="${p.id}" data-name="${name}" data-code="${code}" data-barcode="${barcode}" data-price="${p.price}" data-unit="${unit}" data-prices='${pricesJson.replace(/'/g, '&#39;')}'>
                <span class="name">${p.name || 'Product'}</span>
                <span><span class="price">${parseFloat(p.price).toFixed(2)}</span>${p.code ? ` <span class="code">${p.code}</span>` : ''}${p.barcode ? ` <span class="code">${p.barcode}</span>` : ''}</span>
            </div>`;
        }).join('');
        dropdown.innerHTML = list;
        dropdown.querySelectorAll('.product-item').forEach(el => {
            el.addEventListener('click', function() {
                let prices = [];
                try { prices = JSON.parse(this.dataset.prices || '[]'); } catch (e) {}
                const product = {
                    id: parseInt(this.dataset.id, 10),
                    name: this.dataset.name,
                    code: this.dataset.code || '',
                    barcode: this.dataset.barcode || '',
                    price: parseFloat(this.dataset.price),
                    unit: this.dataset.unit || '',
                    prices: prices.length ? prices : [{ label: 'Selling price', price: parseFloat(this.dataset.price) }]
                };
                document.getElementById('productSearch').value = '';
                hideDropdown();
                openPriceModal(product);
            });
        });
        dropdown.classList.add('show');
    }

    function hideDropdown() {
        document.getElementById('productDropdown').classList.remove('show');
    }

    let pendingProduct = null;
    let pendingPriceSelection = null; // { product, price } after user selects price

    function openPriceModal(product, preselectedPrice) {
        pendingProduct = product;
        const productName = product.name || 'Product';
        document.getElementById('priceModalProductName').textContent = productName;
        document.getElementById('priceModalCustomInput').value = '';
        document.getElementById('priceModalCustomWrap').style.display = 'none';

        const optionsContainer = document.getElementById('priceModalOptions');
        const prices = product.prices && product.prices.length ? product.prices : [{ label: 'Selling price', price: product.price }];
        const priceVal = preselectedPrice != null ? parseFloat(preselectedPrice) : NaN;
        optionsContainer.innerHTML = prices.map((pr, i) => {
            const p = parseFloat(pr.price);
            const selected = !isNaN(priceVal) ? p === priceVal : (i === 0);
            return `<div class="price-option ${selected ? 'selected' : ''}" data-price-type="fixed" data-price="${p}">
                <span class="label">${(pr.label || 'Price').replace(/</g, '&lt;')}</span>
                <span class="amount">${p.toFixed(2)}</span>
            </div>`;
        }).join('');
        const isCustomPreselected = !isNaN(priceVal) && prices.every(pr => parseFloat(pr.price) !== priceVal);
        if (isCustomPreselected) {
            document.getElementById('priceOptCustom').classList.add('selected');
            document.getElementById('priceModalCustomWrap').style.display = 'block';
            document.getElementById('priceModalCustomInput').value = priceVal.toFixed(2);
        }

        optionsContainer.querySelectorAll('.price-option').forEach(opt => {
            opt.addEventListener('click', function() {
                optionsContainer.querySelectorAll('.price-option').forEach(o => o.classList.remove('selected'));
                document.getElementById('priceOptCustom').classList.remove('selected');
                this.classList.add('selected');
                document.getElementById('priceModalCustomWrap').style.display = 'none';
            });
        });

        if (!isCustomPreselected) document.getElementById('priceOptCustom').classList.remove('selected');
        document.getElementById('priceOptCustom').onclick = function() {
            optionsContainer.querySelectorAll('.price-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('priceModalCustomWrap').style.display = 'block';
            document.getElementById('priceModalCustomInput').focus();
        };
        document.getElementById('priceOptCustom').classList.remove('selected');
        if (isCustomPreselected) document.getElementById('priceOptCustom').classList.add('selected');
        document.getElementById('priceModal').classList.add('show');
    }

    function closePriceModal() {
        document.getElementById('priceModal').classList.remove('show');
        pendingProduct = null;
        // do not clear pendingPriceSelection here – it is used when opening the quantity modal
    }

    function getSelectedPrice() {
        const priceModalEl = document.getElementById('priceModal');
        if (!priceModalEl) return null;
        const selected = priceModalEl.querySelector('.price-option.selected');
        if (!selected) return null;
        if (selected.dataset.priceType === 'fixed') return parseFloat(selected.dataset.price);
        if (selected.dataset.priceType === 'custom') {
            const v = parseFloat(document.getElementById('priceModalCustomInput').value);
            return isNaN(v) || v < 0 ? null : v;
        }
        return null;
    }

    function confirmPriceAndOpenQtyModal() {
        if (!pendingProduct) return;
        const price = getSelectedPrice();
        if (price == null) {
            alert('Please select a price or enter a valid custom amount.');
            return;
        }
        pendingPriceSelection = { product: pendingProduct, price: price };
        closePriceModal();
        openQtyModal();
    }

    function openQtyModal() {
        if (!pendingPriceSelection) return;
        const { product, price } = pendingPriceSelection;
        const productName = product.name || 'Product';
        const priceStr = parseFloat(price).toFixed(2);
        document.getElementById('qtyModalSummary').textContent = 'Price: Rs. ' + priceStr;
        document.getElementById('qtyModalProductLine').textContent = productName + ' @ Rs. ' + priceStr;
        const qtyVal = nextQty <= 0 ? 1 : nextQty;
        document.getElementById('qtyModalQty').value = String(qtyVal);
        document.getElementById('qtyModal').classList.add('show');
        setTimeout(function() { document.getElementById('qtyModalQty').focus(); }, 50);
    }

    function closeQtyModal() {
        document.getElementById('qtyModal').classList.remove('show');
        pendingPriceSelection = null;
    }

    function qtyModalBackToPrice() {
        if (!pendingPriceSelection) return;
        const product = pendingPriceSelection.product;
        const price = pendingPriceSelection.price;
        closeQtyModal();
        openPriceModal(product, price);
    }

    function confirmQtyAndAddToCart() {
        if (!pendingPriceSelection) return;
        const qtyInput = document.getElementById('qtyModalQty').value;
        const qty = parseFloat(qtyInput);
        if (isNaN(qty) || qty <= 0) {
            alert('Please enter a valid quantity.');
            return;
        }
        const { product, price } = pendingPriceSelection;
        addToCart({
            id: product.id,
            name: product.name,
            barcode: product.barcode,
            price: price,
            unit: product.unit || ''
        }, qty);
        closeQtyModal();
    }

    function addToCart(product, qtyOverride) {
        const qty = qtyOverride != null ? qtyOverride : (nextQty <= 0 ? 1 : nextQty);
        const existing = cart.find(i => i.id === product.id && i.price === product.price);
        if (existing) {
            existing.qty += qty;
            existing.total = existing.qty * existing.price;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                barcode: product.barcode,
                price: product.price,
                unit: product.unit,
                qty: qty,
                total: qty * product.price
            });
        }
        nextQty = 1;
        renderCart();
        updateTotals();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
        updateTotals();
    }

    function renderCart() {
        const tbody = document.getElementById('cartBody');
        tbody.innerHTML = cart.map((item, i) => `
            <div class="table-row">
                <div>${i + 1}</div>
                <div>${item.barcode || '-'}</div>
                <div>${item.name}</div>
                <div>${parseFloat(item.price).toFixed(2)}</div>
                <div>${parseFloat(item.qty).toFixed(2)}</div>
                <div>${item.unit}</div>
                <div>${parseFloat(item.total).toFixed(2)}</div>
                <button type="button" class="table-delete-btn" onclick="removeFromCart(${i})">Del</button>
            </div>
        `).join('');
    }

    function updateTotals() {
        const subtotal = cart.reduce((s, i) => s + i.total, 0);
        const total = Math.max(0, subtotal - discountAmount);
        document.getElementById('subtotalDisplay').textContent = parseFloat(subtotal).toFixed(2);
        document.getElementById('discountDisplay').textContent = parseFloat(discountAmount).toFixed(2);
        document.getElementById('totalDisplay').textContent = parseFloat(total).toFixed(2);
    }

    function numpadInput(v) {
        const qtyModal = document.getElementById('qtyModal');
        if (qtyModal && qtyModal.classList.contains('show')) {
            const inp = document.getElementById('qtyModalQty');
            if (inp) {
                const cur = inp.value;
                inp.value = (cur === '' || cur === '0') && v !== '.' ? v : (cur + v);
            }
            return;
        }
        if (document.activeElement && (document.activeElement.id === 'productSearch' || document.activeElement.id === 'customerName' || document.activeElement.id === 'customerPhone')) {
            document.activeElement.value += v;
            return;
        }
        nextQty = (nextQty === 0 && v !== '.') ? v : (nextQty + '' + v);
        nextQty = parseFloat(nextQty) || 0;
    }

    function numpadClear() {
        const qtyModal = document.getElementById('qtyModal');
        if (qtyModal && qtyModal.classList.contains('show')) {
            const inp = document.getElementById('qtyModalQty');
            if (inp) inp.value = '1';
            return;
        }
        nextQty = 1;
    }

    function clearBill() {
        if (cart.length && !confirm('Clear all items from bill?')) return;
        cart = [];
        discountAmount = 0;
        nextQty = 1;
        renderCart();
        updateTotals();
    }

    function printBill() {
        if (cart.length === 0) {
            alert('Add at least one product to the bill before printing.');
            return;
        }
        const subtotal = cart.reduce((s, i) => s + i.total, 0);
        const total = Math.max(0, subtotal - discountAmount);
        const inv = document.getElementById('invoiceNo').value;
        const date = document.getElementById('dateField').value;
        const time = document.getElementById('timeField').value;
        const customer = document.getElementById('customerName').value || '-';
        const rows = cart.map((item, i) =>
            `<tr><td>${i + 1}</td><td>${item.name}</td><td>${item.unit}</td><td>${parseFloat(item.qty).toFixed(2)}</td><td>${parseFloat(item.price).toFixed(2)}</td><td>${parseFloat(item.total).toFixed(2)}</td></tr>`
        ).join('');
        const receipt = document.getElementById('receipt-print');
        receipt.innerHTML = `
            <div class="receipt-paper">
                <h2>${storeName}</h2>
                <div class="meta">Invoice: ${inv} | ${date} ${time}</div>
                <div class="meta">Customer: ${customer}</div>
                <table>
                    <thead><tr><th>#</th><th>Item</th><th>Unit</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                    <tbody>${rows}</tbody>
                </table>
                <div class="total-row">Subtotal: ${parseFloat(subtotal).toFixed(2)}</div>
                ${discountAmount > 0 ? `<div>Discount: -${parseFloat(discountAmount).toFixed(2)}</div>` : ''}
                <div class="total-row">Total: ${parseFloat(total).toFixed(2)}</div>
                <div class="thanks">Thank you for your purchase!</div>
            </div>
        `;
        receipt.style.display = 'block';
        window.print();
        receipt.style.display = 'none';
    }

    document.getElementById('productSearch').addEventListener('input', function() {
        const q = this.value.trim();
        if (q) {
            showDropdown(getProductList(this.value));
        } else {
            hideDropdown();
        }
    });

    document.getElementById('productSearch').addEventListener('focus', function() {
        if (this.value.trim()) showDropdown(getProductList(this.value));
    });

    document.getElementById('productSearch').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const items = getProductList(this.value);
            if (items.length > 0) {
                const p = items[0];
                const product = {
                    id: p.id,
                    name: p.name || 'Product',
                    code: p.code || '',
                    barcode: p.barcode || '',
                    price: p.price,
                    unit: p.unit || '',
                    prices: p.prices && p.prices.length ? p.prices : [{ label: 'Selling price', price: p.price }]
                };
                this.value = '';
                hideDropdown();
                openPriceModal(product);
            } else if (this.value.trim()) {
                alert('No product found.');
            }
            e.preventDefault();
        }
        if (e.key === 'Escape') hideDropdown();
    });

    document.addEventListener('click', function(e) {
        if (!document.getElementById('searchSection').contains(e.target)) hideDropdown();
    });

    document.addEventListener('keydown', function(e) {
        const qtyModal = document.getElementById('qtyModal');
        const qtyModalOpen = qtyModal && qtyModal.classList.contains('show');
        if (qtyModalOpen) {
            if (e.key === 'Escape') {
                closeQtyModal();
                e.preventDefault();
                return;
            }
            if (e.key === 'Enter' && !e.ctrlKey && !e.metaKey) {
                confirmQtyAndAddToCart();
                e.preventDefault();
                return;
            }
            return;
        }

        const priceModal = document.getElementById('priceModal');
        const priceModalOpen = priceModal && priceModal.classList.contains('show');
        if (priceModalOpen) {
            const inModalInput = document.activeElement && document.activeElement.id === 'priceModalCustomInput';
            if (e.key === 'Escape') {
                closePriceModal();
                e.preventDefault();
                return;
            }
            if (e.key === 'Enter' && !e.ctrlKey && !e.metaKey) {
                confirmPriceAndOpenQtyModal();
                e.preventDefault();
                return;
            }
            if (inModalInput) return;
            const opts = priceModal.querySelectorAll('.price-option[data-price-type="fixed"]');
            if (opts.length && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
                const idx = Array.from(opts).findIndex(o => o.classList.contains('selected'));
                let next = e.key === 'ArrowDown' ? idx + 1 : idx - 1;
                if (next < 0) next = opts.length - 1;
                if (next >= opts.length) next = 0;
                opts.forEach(o => o.classList.remove('selected'));
                opts[next].classList.add('selected');
                document.getElementById('priceModalCustomWrap').style.display = 'none';
                e.preventDefault();
                return;
            }
            if (opts.length && e.key >= '1' && e.key <= '9') {
                const num = parseInt(e.key, 10);
                if (num <= opts.length) {
                    opts.forEach(o => o.classList.remove('selected'));
                    opts[num - 1].classList.add('selected');
                    document.getElementById('priceModalCustomWrap').style.display = 'none';
                }
                e.preventDefault();
                return;
            }
            return;
        }

        if (e.key === 'F2') {
            document.getElementById('productSearch').focus();
            e.preventDefault();
            return;
        }
        if (e.key === 'Escape') {
            hideDropdown();
            e.preventDefault();
            return;
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            printBill();
            e.preventDefault();
            return;
        }
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
            clearBill();
            e.preventDefault();
        }
    });

    window.addEventListener('load', function() {
        const now = new Date();
        document.getElementById('dateField').value = now.toLocaleDateString();
        document.getElementById('timeField').value = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        renderCart();
        document.getElementById('productSearch').focus();
    });
</script>
</body>
</html>
