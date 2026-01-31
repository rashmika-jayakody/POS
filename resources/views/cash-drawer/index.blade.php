<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cash Drawer - POS System</title>

<style>
* {
    box-sizing: border-box;
}

body{
    margin:0;
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
    background: #e8e8e8;
    font-size:13px;
    min-height: 100vh;
    padding: 10px;
}

.pos-container{
    width:100%;
    max-width:1400px;
    height:calc(100vh - 20px);
    margin:auto;
    background: #ffffff;
    display:flex;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* LEFT SIDE */
.left{
    flex:3;
    border-right:1px solid #e0e0e0;
    display: flex;
    flex-direction: column;
    background: white;
}

/* TOP INFO BAR */
.top-info{
    background: #f5f5f5;
    padding:12px;
    border-bottom:1px solid #ddd;
    display:grid;
    grid-template-columns: repeat(6,1fr);
    gap:8px;
}

.top-info input {
    padding: 8px 12px;
    border: 1px solid #ccc;
    font-size: 12px;
    border-radius: 4px;
    background: white;
    color: #333;
}

.top-info input::placeholder {
    color: #999;
}

/* ADD PRODUCT SECTION */
.add-product-section {
    padding: 12px;
    background: #d3d3d3;
    border-bottom: 1px solid #ddd;
}

.product-input-group {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 8px;
    margin-bottom: 8px;
}

.product-input-group input {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 12px;
    background: white;
}

.product-input-group button {
    background: #000;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.2s;
    font-size: 14px;
    padding: 8px 16px;
}

.product-input-group button:hover {
    background: #222;
}

/* ITEM TABLE */
.table-header, .table-row{
    display:grid;
    grid-template-columns: 50px 70px 1.5fr 90px 70px 70px 70px 90px 50px;
    border-bottom:1px solid #e0e0e0;
    padding:8px 12px;
    align-items: center;
}

.table-header{
    background: #333;
    color:#fff;
    font-weight:bold;
    font-size: 12px;
    text-transform: uppercase;
    position: sticky;
    top: 0;
}

.table-row{
    background: white;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s;
}

.table-row:hover {
    background: #f5f5f5;
}

.table-row.selected{
    background: #e8e8e8;
    color:#333;
    font-weight: bold;
}

.table-delete-btn {
    background: #ff6b6b;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 4px 8px;
    cursor: pointer;
    font-size: 11px;
}

.table-delete-btn:hover {
    background: #ff5252;
}

.table-scroll {
    overflow-y: auto;
    flex: 1;
}

/* NUMPAD SECTION */
.numpad-section {
    padding: 12px;
    background: #f9f9f9;
    border-top: 1px solid #ddd;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.numpad-label {
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
    font-size: 12px;
}

.numpad-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    flex: 1;
}

.numpad-btn {
    padding: 20px;
    border: 1px solid #333;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    font-size: 28px;
    background: #000;
    color: white;
    transition: all 0.2s;
}

.numpad-btn:hover {
    background: #222;
    color: white;
    border-color: #555;
}

.numpad-btn.clear {
    background: #ff6b6b;
    color: white;
    border-color: #ff6b6b;
    font-size: 24px;
}

.numpad-btn.clear:hover {
    background: #ff5252;
    border-color: #ff5252;
}

/* QUICK LINKS SECTION */
.quick-links-section {
    padding: 12px;
    background: #f0f0f0;
    border-top: 1px solid #ddd;
}

.quick-links-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.quick-link-btn {
    padding: 12px;
    border: 1px solid #999;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    font-size: 12px;
    background: white;
    color: #333;
    transition: all 0.2s;
    text-transform: uppercase;
}

.quick-link-btn:hover {
    background: #333;
    color: white;
    border-color: #333;
}

.quick-link-btn.red {
    background: #ff6b6b;
    color: white;
    border-color: #ff6b6b;
}

.quick-link-btn.red:hover {
    background: #ff5252;
}

.quick-link-btn.orange {
    background: #ffa500;
    color: white;
    border-color: #ffa500;
}

.quick-link-btn.orange:hover {
    background: #ff9500;
}

.quick-link-btn.blue {
    background: #4a90e2;
    color: white;
    border-color: #4a90e2;
}

.quick-link-btn.blue:hover {
    background: #3a80d2;
}/* RIGHT PANEL */
.right{
    flex:1.2;
    background: white;
    display:flex;
    flex-direction:column;
    border-left: 1px solid #e0e0e0;
}

.panel-title{
    background: #333;
    color:white;
    padding:12px;
    font-weight:bold;
    text-align:center;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.payment-row{
    display:flex;
    justify-content:space-between;
    padding:10px 12px;
    border-bottom:1px solid #f0f0f0;
    align-items: center;
}

.payment-row-label {
    color: #666;
    font-weight: 500;
}

.payment-value{
    font-size:16px;
    font-weight:bold;
    color:#333;
}

.payment-value.secondary {
    color: #666;
}

.big-value{
    font-size:24px;
    font-weight:bold;
    color: #333;
}

.pay-btn{
    background: #333;
    color:white;
    font-size:16px;
    padding:14px;
    border:none;
    width:100%;
    cursor:pointer;
    font-weight:bold;
    margin-top:auto;
    transition: background 0.2s;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.pay-btn:hover {
    background: #222;
}

.pay-btn:active {
    background: #111;
}

.payment-content {
    flex: 1;
    overflow-y: auto;
}

.divider {
    height: 1px;
    background: #e0e0e0;
    margin: 8px 12px;
}

/* LOYALTY MODAL */
.loyalty-modal {
    display: flex;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
}

.loyalty-modal-content {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 400px;
    text-align: center;
}

.loyalty-modal-content h2 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 24px;
}

.loyalty-modal-content p {
    margin: 0 0 20px 0;
    color: #666;
    font-size: 14px;
}

.loyalty-modal-content input {
    width: 100%;
    padding: 12px;
    margin: 0 0 20px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.loyalty-modal-content input::placeholder {
    color: #999;
}

.loyalty-modal-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.loyalty-modal-btn {
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    transition: background 0.2s;
}

.loyalty-modal-btn.confirm {
    background: #333;
    color: white;
}

.loyalty-modal-btn.confirm:hover {
    background: #222;
}

.loyalty-modal-btn.skip {
    background: #f0f0f0;
    color: #333;
    border: 1px solid #ccc;
}

.loyalty-modal-btn.skip:hover {
    background: #e0e0e0;
}

/* SCROLLBAR STYLING */
.table-scroll::-webkit-scrollbar,
.payment-content::-webkit-scrollbar {
    width: 6px;
}

.table-scroll::-webkit-scrollbar-track,
.payment-content::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-scroll::-webkit-scrollbar-thumb,
.payment-content::-webkit-scrollbar-thumb {
    background: #999;
    border-radius: 3px;
}

.table-scroll::-webkit-scrollbar-thumb:hover,
.payment-content::-webkit-scrollbar-thumb:hover {
    background: #666;
}
</style>
</head>

<script>
    let numpadValue = '';
    let loyaltyPhone = '';

    function confirmLoyalty() {
        const phone = document.getElementById('loyaltyPhone').value;
        if (phone.trim()) {
            loyaltyPhone = phone;
            console.log('Loyalty customer phone:', phone);
        }
        document.getElementById('loyaltyModal').style.display = 'none';
    }

    function skipLoyalty() {
        document.getElementById('loyaltyModal').style.display = 'none';
        console.log('Loyalty customer skipped');
    }

    function numpadInput(value) {
        numpadValue += value;
        console.log('Keypad:', numpadValue);
    }

    function numpadClear() {
        numpadValue = '';
        console.log('Cleared');
    }

    function applyDiscount() {
        alert('Apply discount functionality');
    }

    function applyTax() {
        alert('Apply tax functionality');
    }

    function holdBill() {
        alert('Bill held successfully');
    }

    function viewHistory() {
        alert('View transaction history');
    }

    function printReceipt() {
        alert('Receipt printing...');
    }

    function clearBill() {
        if (confirm('Clear all items from bill?')) {
            document.querySelectorAll('.table-row').forEach(row => row.remove());
        }
    }

    function addProduct() {
        const barcode = document.getElementById('barcodeInput').value;
        const qty = document.getElementById('qtyInput').value;
        const price = document.getElementById('priceInput').value;

        if (!barcode || !price) {
            alert('Please enter barcode and price');
            return;
        }

        // Create new row
        const tableScroll = document.querySelector('.table-scroll');
        const newRow = document.createElement('div');
        newRow.className = 'table-row';
        newRow.innerHTML = `
            <div>${barcode}</div>
            <div>New Product</div>
            <div>${parseFloat(price).toFixed(2)}</div>
            <div>${parseFloat(qty).toFixed(3)}</div>
            <div>1</div>
            <div>0.00</div>
            <div>${(qty * price).toFixed(2)}</div>
            <button class="table-delete-btn" onclick="this.parentElement.remove()">Delete</button>
        `;
        tableScroll.appendChild(newRow);

        // Clear inputs
        document.getElementById('barcodeInput').value = '';
        document.getElementById('qtyInput').value = '1';
        document.getElementById('priceInput').value = '';
        document.getElementById('barcodeInput').focus();
    }

    function processPayment() {
        alert('Payment processed successfully!');
    }

    // Set current date and time
    window.addEventListener('load', function() {
        const now = new Date();
        document.querySelectorAll('input[placeholder="Date"]')[0].value = now.toLocaleDateString();
        document.querySelectorAll('input[placeholder="Time"]')[0].value = now.toLocaleTimeString();
    });

    // Allow Enter key to add product
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('priceInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') addProduct();
        });
    });
</script>

<body>

<!-- LOYALTY CUSTOMER MODAL -->
<div id="loyaltyModal" class="loyalty-modal">
    <div class="loyalty-modal-content">
        <h2>Loyalty Customer</h2>
        <p>Enter customer phone number (optional)</p>
        <input type="text" id="loyaltyPhone" placeholder="Enter phone number...">
        <div class="loyalty-modal-buttons">
            <button class="loyalty-modal-btn confirm" onclick="confirmLoyalty()">Confirm</button>
            <button class="loyalty-modal-btn skip" onclick="skipLoyalty()">Skip</button>
        </div>
    </div>
</div>

<div class="pos-container">

<!-- LEFT -->
<div class="left">

    <div class="top-info">
        <input placeholder="Invoice No" readonly>
        <input placeholder="Customer Name">
        <input placeholder="Phone">
        <input placeholder="Date" readonly>
        <input placeholder="User" readonly>
        <input placeholder="Time" readonly>
    </div>

    <div class="table-header">
        <div>#</div>
        <div>Barcode</div>
        <div>Item Name</div>
        <div>Unit Price</div>
        <div>Qty</div>
        <div>PK</div>
        <div>Disc</div>
        <div>Total</div>
        <div>Action</div>
    </div>

    <div class="table-scroll">
        <div class="table-row">
            <div>1</div>
            <div>1018</div>
            <div>Sugar 1Kg</div>
            <div>180.00</div>
            <div>1.000</div>
            <div>1Kg</div>
            <div>0.00</div>
            <div>180.00</div>
            <button class="table-delete-btn">Delete</button>
        </div>

        <div class="table-row selected">
            <div>2</div>
            <div>1019</div>
            <div>Rice 1Kg</div>
            <div>500.00</div>
            <div>1.000</div>
            <div>1Kg</div>
            <div>130.00</div>
            <div>370.00</div>
            <button class="table-delete-btn">Delete</button>
        </div>

        <div class="table-row">
            <div>3</div>
            <div>1020</div>
            <div>Dal 1Kg</div>
            <div>450.00</div>
            <div>2.000</div>
            <div>1Kg</div>
            <div>0.00</div>
            <div>900.00</div>
            <button class="table-delete-btn">Delete</button>
        </div>
    </div>

    <!-- ADD PRODUCT SECTION -->
    <div class="add-product-section">
        <div class="product-input-group">
            <input type="text" id="barcodeInput" placeholder="Scan barcode or enter product name...">
            <input type="number" id="qtyInput" placeholder="Qty" value="1" min="1">
            <input type="number" id="priceInput" placeholder="Price">
            <button onclick="addProduct()">ADD</button>
        </div>
    </div>

    <!-- KEYPAD SECTION -->
    <div class="numpad-section">
        <div class="numpad-label">NUMERIC KEYPAD</div>
        <div class="numpad-grid">
            <button class="numpad-btn" onclick="numpadInput('1')">1</button>
            <button class="numpad-btn" onclick="numpadInput('2')">2</button>
            <button class="numpad-btn" onclick="numpadInput('3')">3</button>
            <button class="numpad-btn" onclick="numpadInput('4')">4</button>
            <button class="numpad-btn" onclick="numpadInput('5')">5</button>
            <button class="numpad-btn" onclick="numpadInput('6')">6</button>
            <button class="numpad-btn" onclick="numpadInput('7')">7</button>
            <button class="numpad-btn" onclick="numpadInput('8')">8</button>
            <button class="numpad-btn" onclick="numpadInput('9')">9</button>
            <button class="numpad-btn" onclick="numpadInput('0')">0</button>
            <button class="numpad-btn" onclick="numpadInput('.')">.</button>
            <button class="numpad-btn clear" onclick="numpadClear()">CLR</button>
        </div>
    </div>

    <!-- QUICK LINKS SECTION -->
    <div class="quick-links-section">
        <div class="quick-links-grid">
            <button class="quick-link-btn blue" onclick="applyDiscount()">Discount</button>
            <button class="quick-link-btn blue" onclick="applyTax()">Tax</button>
            <button class="quick-link-btn orange" onclick="holdBill()">Hold Bill</button>
            <button class="quick-link-btn blue" onclick="viewHistory()">History</button>
            <button class="quick-link-btn blue" onclick="printReceipt()">Print</button>
            <button class="quick-link-btn red" onclick="clearBill()">Clear</button>
        </div>
    </div>

</div>

<!-- RIGHT -->
<div class="right">

    <div class="payment-content">
        <div class="panel-title">PAYMENT TYPE</div>

        <div class="payment-row">
            <span class="payment-row-label">Method:</span>
            <span class="payment-value">CASH</span>
        </div>

        <div class="divider"></div>

        <div class="panel-title">BILL SUMMARY</div>

        <div class="payment-row">
            <span class="payment-row-label">Subtotal:</span>
            <span class="payment-value">₹1,450.00</span>
        </div>

        <div class="payment-row">
            <span class="payment-row-label">Discount:</span>
            <span class="payment-value secondary">-₹130.00</span>
        </div>

        <div class="payment-row">
            <span class="payment-row-label">Tax (5%):</span>
            <span class="payment-value">₹66.00</span>
        </div>

        <div class="payment-row" style="background: #f0f0f0; padding: 12px;">
            <span class="payment-row-label">Bill Amount:</span>
            <span style="font-size: 20px; font-weight: bold; color: #333;">₹1,386.00</span>
        </div>

        <div class="divider"></div>

        <div class="panel-title">TRANSACTION</div>

        <div class="payment-row">
            <span class="payment-row-label">Received:</span>
            <span class="big-value">₹1,500.00</span>
        </div>

        <div class="payment-row">
            <span class="payment-row-label">Balance:</span>
            <span class="big-value" style="color: #16a34a;">₹114.00</span>
        </div>
    </div>

    <button class="pay-btn" onclick="processPayment()">💳 PAY & PRINT</button>

    <!-- LOGO SECTION -->
    <div style="padding: 20px; text-align: center; border-top: 1px solid #ddd;">
        <img src="/assets/logo.png" alt="Company Logo" style="max-width: 180px; height: auto; display: block; margin: 0 auto;">
    </div>

</div>

</div>

</body>
</html>