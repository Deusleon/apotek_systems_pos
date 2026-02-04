<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mobile POS</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            overflow-x: hidden;
        }

        /* Desktop Warning Screen */
        #desktop-warning {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #3490dc 0%, #2176bd 100%);
            color: white;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            text-align: center;
            padding: 20px;
        }

        #desktop-warning .warning-content {
            max-width: 500px;
        }

        #desktop-warning h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        #desktop-warning p {
            font-size: 1.5rem;
            opacity: 0.9;
        }

        /* Mobile POS Container */
        #mobile-pos {
            display: block;
            max-width: 480px;
            margin: 0 auto;
            height: 100vh;
            background: white;
        }

        /* Header */
        .pos-header {
            background: linear-gradient(135deg, #3490dc 0%, #2176bd 100%);
            color: white;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .pos-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Search Bar */
        .search-bar {
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: white;
            border-bottom: 1px solid #e0e0e0;
        }

        .input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .input:focus {
            outline: none;
            border-color: #3490dc;
        }

        /* Complete Sale Button */
        .complete-sale-btn {
            width: calc(100% - 30px);
            margin: 15px;
            padding: 18px;
            background: linear-gradient(135deg, #386fc1 0%, #2d5399 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(56, 193, 114, 0.3);
            transition: all 0.3s;
        }

        .complete-sale-btn:active {
            transform: scale(0.98);
        }

        .complete-sale-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Loading Spinner */
        .spinner {
            display: none;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3490dc;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Receipt Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }

            #receipt-print,
            #receipt-print * {
                visibility: visible;
            }

            #receipt-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }

        #receipt-print {
            display: none;
            font-family: 'Courier New', monospace;
            width: 80mm;
            padding: 10mm;
            background: white;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }

        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .receipt-info {
            font-size: 11px;
            margin: 2px 0;
        }

        .receipt-items {
            margin: 10px 0;
        }

        .receipt-item {
            font-size: 11px;
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
        }

        .receipt-summary {
            border-top: 2px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
        }

        .receipt-total {
            font-size: 14px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 15px;
            font-size: 11px;
            border-top: 2px dashed #000;
            padding-top: 10px;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .toast.show {
            display: block;
            animation: slideUp 0.3s ease-out;
        }

        .toast.success {
            background: #38c172;
        }

        .toast.error {
            background: #dc3545;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Media Query for Desktop Detection */
        @media (min-width: 481px) {
            #desktop-warning {
                display: flex !important;
            }

            #mobile-pos {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Desktop Warning -->
    <div id="desktop-warning">
        <div class="warning-content">
            {{-- <h1>📱</h1> --}}
            <h1>Mobile Only</h1>
            <p>This POS system is only available on mobile devices</p>
            <p style="margin-top: 20px; font-size: 1rem;">Please access this page from your mobile phone.</p>
        </div>
    </div>

    <!-- Mobile POS -->
    <div id="mobile-pos">
        <!-- Header -->
        <div class="pos-header">
            <h1>Mobile POS</h1>
        </div>

        <div class="search-bar">
            <div style="display: flex; flex-direction: column;">
                <label for="nameInput">Item Name</label>
                <input type="text" value="Taka" id="nameInput" class="input" placeholder="Item name" readonly
                    autocomplete="off">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="customerInput">Customer Name</label>
                <input type="text" id="customerInput" class="input" placeholder="Customer name" autocomplete="off">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="weightInput">Weight (kg)</label>
                <input type="text" id="weightInput" class="input" placeholder="Weight (kg)" autocomplete="off">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="priceInput">Price</label>
                <input type="text" id="priceInput" class="input" placeholder="Price" autocomplete="off">
            </div>
        </div>

        <div class="products-container">
            <div class="spinner" id="productsSpinner"></div>
            <div class="products-grid" id="productsGrid">
                <div class="empty-state">
                    <div class="empty-state-icon"></div>
                </div>
            </div>
        </div>

        <div class="cart-container">
            <!-- Complete Sale Button -->
            <button class="complete-sale-btn" id="completeSaleBtn" disabled>
                Save & Print
            </button>
        </div>
    </div>

    <!-- Receipt Print Template -->
    <div id="receipt-print">
        <div class="receipt-header">
            <div class="receipt-title">RECEIPT</div>
            <div class="receipt-info" id="receiptNumber">Receipt: #000000</div>
            <div class="receipt-info" id="receiptDate">Date: {{ date('Y-m-d H:i:s') }}</div>
            <div class="receipt-info">Cashier: {{ Auth::user()->name }}</div>
        </div>

        <div class="receipt-items" id="receiptItems">
            <!-- Items will be inserted here -->
        </div>

        <div class="receipt-summary">
            <div class="receipt-item">
                <span>Subtotal:</span>
                <span id="receiptSubtotal">0.00</span>
            </div>
            <div class="receipt-item">
                <span>VAT ({{ $vat * 100 }}%):</span>
                <span id="receiptVat">0.00</span>
            </div>
            <div class="receipt-total">
                <span>TOTAL:</span>
                <span id="receiptTotal">0.00</span>
            </div>
        </div>

        <div class="receipt-footer">
            <div>Payment: CASH</div>
            <div style="margin-top: 10px;">Thank you for your business!</div>
            <div>Please come again</div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script>

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            document.getElementById('completeSaleBtn').addEventListener('click', completeSale);
            document.getElementById('priceInput').addEventListener('input', checkInputsFilled);
            document.getElementById('weightInput').addEventListener('input', checkInputsFilled);
        }

        function checkInputsFilled() {
            const price = document.getElementById('priceInput').value.trim();
            const weight = document.getElementById('weightInput').value.trim();

            const completeSaleBtn = document.getElementById('completeSaleBtn');

            if (price !== "" && weight !== "") {
                completeSaleBtn.disabled = false;
            } else {
                completeSaleBtn.disabled = true;
            }
        }

        // Complete sale
        function completeSale() {
            const completeSaleBtn = document.getElementById('completeSaleBtn');
            completeSaleBtn.disabled = true;
            completeSaleBtn.textContent = 'Processing...';

            const customer_name = document.getElementById('customerInput').value.trim();
            const price = parseFloat(document.getElementById('priceInput').value.replace(/[^0-9.-]+/g, ''));
            const weight = parseFloat(document.getElementById('weightInput').value.replace(/[^0-9.-]+/g, ''));

            const saleData = {
                item: "Taka",
                customer: customer_name || '',
                price: price,
                weight: weight
            };

            fetch('{{ route("mobile-pos.storeMobileSale") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(saleData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Completed successfully!', 'success');
                        printReceipt(data.receipt_number, saleData);
                        document.getElementById('customerInput').value = '';
                        document.getElementById('weightInput').value = '';
                        document.getElementById('priceInput').value = '';
                    } else {
                        showToast(data.message || 'An error occured while saving', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occured while saving', 'error');
                })
                .finally(() => {
                    completeSaleBtn.disabled = false;
                    completeSaleBtn.textContent = 'Save & Print';
                });
        }

        // Print receipt
        function printReceipt(receiptNumber, saleData) {
            document.getElementById('receiptNumber').textContent = `Receipt: ${receiptNumber}`;
            document.getElementById('receiptDate').textContent = `Date: ${new Date().toLocaleString()}`;

            const receiptItemsHtml = `
                <div class="receipt-item">
                    <div>
                        <div>${saleData.item}</div>
                        <div style="font-size: 10px;">${saleData.weight}kg -> ${saleData.price}</div>
                    </div>
                </div>
            `;

            document.getElementById('receiptItems').innerHTML = receiptItemsHtml;
            document.getElementById('receiptTotal').textContent = saleData.price.toFixed(2);

            // Small delay for DOM update
            setTimeout(() => {
                window.print();
            }, 400);
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 2000);
        }
    </script>
</body>

</html>