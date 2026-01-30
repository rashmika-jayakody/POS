<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f1e8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-bg {
            background-color: #2c3e50;
            color: white;
        }

        .table-header {
            background-color: #34495e;
            color: white;
            font-weight: bold;
        }

        .table-row {
            border-bottom: 1px solid #ddd;
            background-color: #f9f7f3;
        }

        .table-row:hover {
            background-color: #fffbf5;
        }

        .table-row.highlighted {
            background-color: #fff8dc;
        }

        .table-row.selected {
            background-color: #b3d9ff;
        }

        .payment-section {
            background-color: #f5f1e8;
            border: 2px solid #34495e;
        }

        .payment-header {
            background-color: #c41e3a;
            color: white;
            font-weight: bold;
            padding: 8px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            border-bottom: 1px solid #d0d0d0;
        }

        .payment-row.highlight {
            background-color: #e8f4ff;
            border: 1px solid #34495e;
        }

        .payment-value {
            font-weight: bold;
            font-size: 18px;
            color: #0066cc;
        }

        .btn-payment {
            background-color: #27ae60;
            color: white;
            font-weight: bold;
            border: none;
            padding: 12px;
            cursor: pointer;
            width: 100%;
            margin-top: 8px;
        }

        .btn-payment:hover {
            background-color: #229954;
        }

        .btn-small {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            border: 1px solid #2c3e50;
            padding: 6px 12px;
            cursor: pointer;
            margin: 4px;
            font-size: 12px;
        }

        .btn-small:hover {
            background-color: #2c3e50;
        }

        .hold-bills-panel {
            background-color: #ecf0f1;
            border: 2px solid #34495e;
            overflow-y: auto;
        }

        .hold-bill-item {
            background-color: #f9f7f3;
            border-bottom: 1px solid #bdc3c7;
            padding: 10px;
            cursor: pointer;
            margin-bottom: 4px;
        }

        .hold-bill-item:hover {
            background-color: #fff8dc;
        }

        .hold-bill-item.selected {
            background-color: #b3d9ff;
            border-left: 4px solid #0066cc;
        }

        .info-box {
            background-color: white;
            border: 1px solid #bdc3c7;
            padding: 8px;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .info-label {
            color: #666;
            font-weight: bold;
        }

        .scrollable {
            overflow-y: auto;
        }

        .hidden-row {
            display: none;
        }

        input[type="text"], input[type="number"], select {
            padding: 4px 8px;
            border: 1px solid #bdc3c7;
            font-size: 12px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="header-bg p-4">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold">Professional POS System</h1>
                <p class="text-gray-300 text-sm">Retail Transaction Management</p>
            </div>
            <div class="text-right">
                <p class="text-gray-300 text-sm">User: Admin | <span id="currentTime">00:00:00</span></p>
                <p class="text-gray-300 text-sm">Date: <span id="currentDate">01/01/2026</span></p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto p-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 h-screen-90">
            <!-- Left Panel - Hold Bills -->
            <div class="lg:col-span-1">
                <div class="payment-section">
                    <div class="payment-header">
                        <i class="fas fa-list mr-2"></i>HOLD BILLS LIST (P7)
                    </div>
                    <div class="info-box">
                        <label class="info-label">User</label>
                        <input type="text" value="Admin" readonly class="w-full">
                    </div>
                    <div class="hold-bills-panel scrollable" style="height: 200px;">
                        <div class="hold-bill-item" onclick="loadBill(1)">
                            <strong>BILL #001</strong>
                            <div class="text-xs text-gray-600">Amount: ₹1,256.70</div>
                            <div class="text-xs text-gray-600">Items: 5</div>
                        </div>
                        <div class="hold-bill-item" onclick="loadBill(2)">
                            <strong>BILL #002</strong>
                            <div class="text-xs text-gray-600">Amount: ₹890.00</div>
                            <div class="text-xs text-gray-600">Items: 3</div>
                        </div>
                        <div class="hold-bill-item" onclick="loadBill(3)">
                            <strong>BILL #003</strong>
                            <div class="text-xs text-gray-600">Amount: ₹2,450.50</div>
                            <div class="text-xs text-gray-600">Items: 7</div>
                        </div>
                        <div class="hold-bill-item" onclick="loadBill(4)">
                            <strong>BILL #004</strong>
                            <div class="text-xs text-gray-600">Amount: ₹567.30</div>
                            <div class="text-xs text-gray-600">Items: 2</div>
                        </div>
                    </div>
                    <div style="padding: 8px;">
                        <button class="btn-small w-full" onclick="clearBills()">Clear</button>
                    </div>
                </div>

                <!-- Info Sections -->
                <div class="mt-4 payment-section">
                    <div class="info-box">
                        <label class="info-label">Barcode</label>
                        <input type="text" placeholder="Scan barcode..." class="w-full">
                    </div>
                    <div class="info-box">
                        <label class="info-label">Item Registration</label>
                        <button class="btn-small w-full">Add Item</button>
                    </div>
                </div>
            </div>

            <!-- Center Panel - Items Table -->
            <div class="lg:col-span-2">
                <div class="bg-white border-2 border-gray-400">
                    <!-- Table Header -->
                    <div class="table-header p-2 text-sm grid grid-cols-10 gap-1">
                        <div class="col-span-1">BARCODE</div>
                        <div class="col-span-3">ITEM NAME</div>
                        <div class="col-span-1">RETAIL PRICE</div>
                        <div class="col-span-1">PRICE</div>
                        <div class="col-span-1">DISC %</div>
                        <div class="col-span-1">QTY</div>
                        <div class="col-span-1">TOTAL AMOUNT</div>
                    </div>

                    <!-- Items List -->
                    <div class="scrollable" style="height: 300px;" id="itemsTable">
                        <div class="table-row p-2 text-sm grid grid-cols-10 gap-1">
                            <div class="col-span-1">BRK001</div>
                            <div class="col-span-3">Biscuit Pack (200g)</div>
                            <div class="col-span-1">180.00</div>
                            <div class="col-span-1">180.00</div>
                            <div class="col-span-1">0.00</div>
                            <div class="col-span-1">1.000</div>
                            <div class="col-span-1 font-bold">180.00</div>
                        </div>
                        <div class="table-row p-2 text-sm grid grid-cols-10 gap-1">
                            <div class="col-span-1">MLK002</div>
                            <div class="col-span-3">Fresh Milk (500ml)</div>
                            <div class="col-span-1">100.00</div>
                            <div class="col-span-1">100.00</div>
                            <div class="col-span-1">0.00</div>
                            <div class="col-span-1">2.000</div>
                            <div class="col-span-1 font-bold">200.00</div>
                        </div>
                        <div class="table-row p-2 text-sm grid grid-cols-10 gap-1">
                            <div class="col-span-1">BRD003</div>
                            <div class="col-span-3">Bread Loaf White</div>
                            <div class="col-span-1">100.00</div>
                            <div class="col-span-1">100.00</div>
                            <div class="col-span-1">0.00</div>
                            <div class="col-span-1">1.650</div>
                            <div class="col-span-1 font-bold">165.00</div>
                        </div>
                        <div class="table-row highlighted p-2 text-sm grid grid-cols-10 gap-1">
                            <div class="col-span-1">EGG004</div>
                            <div class="col-span-3">Eggs (1 dozen)</div>
                            <div class="col-span-1">200.00</div>
                            <div class="col-span-1">200.00</div>
                            <div class="col-span-1">5.00</div>
                            <div class="col-span-1">0.950</div>
                            <div class="col-span-1 font-bold">190.00</div>
                        </div>
                        <div class="table-row selected p-2 text-sm grid grid-cols-10 gap-1">
                            <div class="col-span-1">OIL005</div>
                            <div class="col-span-3">Cooking Oil (1L)</div>
                            <div class="col-span-1">600.00</div>
                            <div class="col-span-1">600.00</div>
                            <div class="col-span-1">0.00</div>
                            <div class="col-span-1">1.000</div>
                            <div class="col-span-1 font-bold">600.00</div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="bg-gray-100 border-t-2 border-gray-400 p-3">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="info-label">Total Items:</div>
                                <div class="text-lg font-bold">6 Items</div>
                            </div>
                            <div class="text-right">
                                <div class="info-label">Total Amount:</div>
                                <div class="text-lg font-bold text-green-700">₹1,335.00</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <button class="btn-small">Delete Item</button>
                    <button class="btn-small">Modify</button>
                    <button class="btn-small">View Invoice</button>
                    <button class="btn-small">Clear Customer</button>
                </div>
            </div>

            <!-- Right Panel - Payment -->
            <div class="lg:col-span-1">
                <div class="payment-section">
                    <!-- Payment Header -->
                    <div class="payment-header">
                        PAYMENT SECTION
                    </div>

                    <!-- Subtotal -->
                    <div class="payment-row">
                        <span>Subtotal:</span>
                        <span id="subtotal" class="font-bold">₹1,335.00</span>
                    </div>

                    <!-- Discount -->
                    <div class="payment-row">
                        <span>Discount %:</span>
                        <input type="number" value="0" class="w-20" min="0" max="100">
                    </div>

                    <div class="payment-row">
                        <span>Discount Amount:</span>
                        <span id="discount" class="font-bold">₹0.00</span>
                    </div>

                    <!-- Tax -->
                    <div class="payment-row">
                        <span>Tax (GST 5%):</span>
                        <span id="tax" class="font-bold">₹66.75</span>
                    </div>

                    <!-- Bill Amount -->
                    <div class="payment-row">
                        <span>Bill Amount:</span>
                        <span id="billAmount" class="font-bold">₹1,401.75</span>
                    </div>

                    <!-- Divider -->
                    <div style="height: 2px; background-color: #34495e; margin: 8px 0;"></div>

                    <!-- Payment Type -->
                    <div class="payment-row">
                        <span>Payment Type:</span>
                    </div>
                    <div style="padding: 0 8px;">
                        <select class="w-full border-2 border-red-600">
                            <option>CASH</option>
                            <option>CARD</option>
                            <option>CHEQUE</option>
                            <option>DIGITAL</option>
                        </select>
                    </div>

                    <!-- Divider -->
                    <div style="height: 2px; background-color: #34495e; margin: 8px 0;"></div>

                    <!-- Received -->
                    <div class="payment-header">Received</div>
                    <div class="payment-row highlight">
                        <span></span>
                        <span class="payment-value">₹1,256.70</span>
                    </div>

                    <!-- Balance -->
                    <div class="payment-header">Balance</div>
                    <div class="payment-row highlight">
                        <span></span>
                        <span class="payment-value" style="color: #ff0000;">₹0.00</span>
                    </div>

                    <!-- Return Amount -->
                    <div class="payment-row">
                        <span>Return Amount:</span>
                        <span id="returnAmount" class="font-bold">₹0.00</span>
                    </div>

                    <!-- Payment Button -->
                    <button class="btn-payment" onclick="processPayment()">
                        <i class="fas fa-check mr-2"></i>PAY & PRINT
                    </button>

                    <!-- Additional Buttons -->
                    <div style="padding: 8px;">
                        <button class="btn-small w-full" style="background-color: #e74c3c;">Special Discount</button>
                        <button class="btn-small w-full" style="background-color: #8e44ad;">Loyalty Card</button>
                        <button class="btn-small w-full" style="background-color: #f39c12;">Receipt Print</button>
                    </div>
                </div>

                <!-- System Actions -->
                <div class="mt-4 payment-section">
                    <div class="payment-header">SYSTEM</div>
                    <div style="padding: 8px;">
                        <button class="btn-small w-full" style="background-color: #e74c3c;">Logout</button>
                        <button class="btn-small w-full" style="background-color: #f39c12;">Hold Bill</button>
                        <button class="btn-small w-full" style="background-color: #34495e;">Settings</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-md text-center shadow-2xl">
            <div class="text-5xl text-green-600 mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Payment Successful!</h2>
            <p class="text-gray-600 mb-6">Transaction completed successfully</p>
            <p class="text-sm text-gray-500 mb-6">Receipt has been printed</p>
            <button onclick="closeModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-2 rounded">
                Continue
            </button>
        </div>
    </div>

    <script>
        // Update time and date
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-IN', { hour12: false });
            document.getElementById('currentDate').textContent = now.toLocaleDateString('en-IN');
        }
        updateTime();
        setInterval(updateTime, 1000);

        function loadBill(billNumber) {
            const items = document.querySelectorAll('.hold-bill-item');
            items.forEach(item => item.classList.remove('selected'));
            event.target.closest('.hold-bill-item').classList.add('selected');
        }

        function clearBills() {
            if (confirm('Clear all held bills?')) {
                document.querySelectorAll('.hold-bill-item').forEach(item => item.remove());
            }
        }

        function processPayment() {
            document.getElementById('successModal').classList.remove('hidden');
            setTimeout(() => {
                closeModal();
                location.reload();
            }, 3000);
        }

        function closeModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        // Calculate totals
        function calculateTotals() {
            const subtotal = 1335.00;
            const discountPercent = 0;
            const discountAmount = (subtotal * discountPercent) / 100;
            const billAmount = subtotal - discountAmount;
            const tax = (billAmount * 5) / 100;
            const total = billAmount + tax;

            document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('discount').textContent = '₹' + discountAmount.toFixed(2);
            document.getElementById('tax').textContent = '₹' + tax.toFixed(2);
            document.getElementById('billAmount').textContent = '₹' + total.toFixed(2);
        }

        calculateTotals();
    </script>
</body>
</html>