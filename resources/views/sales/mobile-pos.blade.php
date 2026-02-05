<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mobile POS</title>

    <link rel="stylesheet" href="{{asset("assets/fonts/fontawesome/css/fontawesome-all.min.css")}}">
    <link rel="stylesheet" href="{{asset("assets/css/style.css")}}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; */
            font-family: Arial, Helvetica, sans-serif;
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
            background: #f5f5f5;
            /* color: white; */
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
            height: 30vh;
            background: white;
        }

        /* Header */
        .pos-header {
            background: #3f4d67;
            color: white;
            padding: 17px;
            text-align: left;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .pos-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .pos-header .mobile-menu {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
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
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .input:focus {
            outline: none;
            border-color: #04a9f5;
        }

        /* Complete Sale Button */
        .complete-sale-btn {
            width: calc(100% - 30px);
            margin: 15px;
            padding: 15px;
            background: #04a9f5;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
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
            /* Hide everything except receipt */
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 80mm !important;
                height: auto !important;
                background: white !important;
            }

            body * {
                visibility: hidden !important;
            }

            #receipt-print,
            #receipt-print * {
                visibility: visible !important;
            }

            #receipt-print {
                display: block !important;
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                width: 80mm !important;
                min-height: auto !important;
                max-height: none !important;
                margin: 0 !important;
                padding: 2mm !important;
                background: white !important;
                font-size: 12px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Hide other elements */
            .pcoded-navbar,
            .pcoded-main-container,
            #desktop-warning,
            #mobile-pos,
            .toast,
            nav {
                display: none !important;
                visibility: hidden !important;
            }

            @page {
                size: 80mm auto;
                margin: 0 !important;
                padding: 0 !important;
            }
        }

        /* Screen styles for receipt (hidden on screen) */
        #receipt-print {
            font-family: Arial, Helvetica, sans-serif;
            width: 80mm;
            padding: 2mm;
            background: white;
            display: none;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 2px;
        }

        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .receipt-info {
            font-size: 14px;
            margin: 2px 0;
        }

        .receipt-info2 {
            font-size: 14px;
            margin: 2px 0;
            text-align: left;
        }

        .receipt-items-header {
            display: flex;
            padding-top: 4px;
            padding-bottom: 4px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
        }

        .receipt-items {
            display: flex;
            margin-top: 10px;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-summary {
            border-top: 2px solid #000;
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
            font-size: 14px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 50px;
            left: 50%;
            width: 300px;
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
            width: 300px;
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
    <!-- [ navigation menu ] start -->
    <nav class="pcoded-navbar brand-red  active-red menu-item-icon-style4 icon-colored">
        <div class="navbar-wrapper">
            <div class="navbar-content scroll-div">
                <ul class="nav pcoded-inner-navbar">
                    @include('layouts.menu')
                </ul>
            </div>
        </div>
    </nav>
    <!-- [ navigation menu ] end -->

    <div class="pcoded-main-container">
        <!-- Desktop Warning -->
        <div id="desktop-warning">
            <div class="warning-content">
                <h1>Mobile Only</h1>
                <p>This POS system is only available on mobile devices</p>
                <p style="margin-top: 20px; font-size: 1rem;">Please access this page from your mobile phone.</p>
                <button
                    style="padding: 10px; margin-top: 20px; background-color: #3490dc; color: white; border: none; border-radius: 4px; cursor: pointer;"
                    onclick="window.location.href='/'">Go to Home</button>
            </div>
        </div>

        <!-- Mobile POS -->
        <div id="mobile-pos">
            <!-- Header -->
            <div class="pos-header">
                <div class="m-header">
                    <a href="index.html" class="b-brand">
                        <div class="b-bg">
                            <i class="feather icon-trending-up"></i>
                        </div>
                        <span class="b-title">APOTEk</span>
                        <a class="mobile-menu" style="color: #ffffff; font-weight: bold; height: 43px; width: auto;" id="mobile-collapse"
                            href="{{ route('logout') }}" class="dud-logout" title="Logout" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                            <i class="feather icon-log-out"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </a>
                </div>
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
                    <input type="text" inputmode="numeric" id="weightInput" class="input" placeholder="Weight (kg)"
                        autocomplete="off">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <label for="priceInput">Price</label>
                    <input type="text" inputmode="numeric" id="priceInput" class="input" placeholder="Price"
                        autocomplete="off">
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
                <div class="receipt-info" id="companyName"></div>
                <div class="receipt-info" id="companyAddress"></div>
                <div class="receipt-info" id="companyPhone"></div>
                <div class="receipt-info" id="companyTin"></div>
                <div class="receipt-info2" id="receiptNumber">Receipt #: #000000</div>
                <div class="receipt-info2" id="receiptDate">Date: {{ date('Y-m-d H:i:s') }}</div>
                <div class="receipt-info2" id="customerName"></div>
                <div class="receipt-info2">Payment: CASH</div>
            </div>

            <div class="receipt-items-header">
                <div style="width: 40%; text-align: left;">Description</div>
                <div style="width: 20%; text-align: center; margin-left: 10px;">Weight</div>
                <div style="width: 40%; text-align: right;">Amount</div>
            </div>
            <div class="receipt-items" id="receiptItems">
                <!-- Items will be inserted here -->
            </div>

            <div class="receipt-summary">
                <div class="receipt-total">
                    <span>TOTAL:</span>
                    <span id="receiptTotal">0.00</span>
                </div>
            </div>

            <div class="receipt-footer">
                <div class="receipt-info">Issued By: {{ Auth::user()->name }}</div>
                <div style="margin-top: 1px;" id="companySlogan">Thank you for your business!</div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>
    </div>

    <script src="{{asset("assets/js/vendor-all.min.js")}}"></script>
    <script src="{{asset("assets/plugins/bootstrap/js/bootstrap.min.js")}}"></script>
    <script src="{{asset("assets/js/pcoded.min.js")}}"></script>

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

        document.getElementById('priceInput').addEventListener('blur', function () {
            let val = this.value.replace(/[^0-9.-]+/g, '');
            if (val !== "") {
                this.value = formatMoney(parseFloat(val));
            }
        });

        document.getElementById('weightInput').addEventListener('blur', function () {
            let val = this.value.replace(/[^0-9.-]+/g, '');
            if (val !== "") {
                this.value = numberWithCommas(parseFloat(val));
            }
        });

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
                        printReceipt(data, saleData);
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
        function printReceipt(data, saleData) {
            console.log('Printing receipt for:', data);
            document.getElementById('receiptNumber').textContent = `Receipt #: ${data.receipt_number}`;
            document.getElementById('receiptDate').textContent = `Date: ${formatDateTime()}`;
            document.getElementById('customerName').textContent = saleData.customer ? `Customer: ${saleData.customer}` : 'Customer: CASH';
            document.getElementById('companyName').textContent = data.company_name;
            document.getElementById('companyAddress').textContent = data.company_address;
            document.getElementById('companyPhone').textContent = data.company_phone;
            document.getElementById('companyTin').textContent = `TIN: ${data.company_tin}`;
            document.getElementById('companySlogan').textContent = data.company_slogan

            const receiptItemsHtml = `
                        <div style="width: 40%; text-align: left;">${saleData.item}</div>
                        <div style="width: 20%; text-align: center;">${numberWithCommas(saleData.weight)}kg</div>
                        <div style="width: 40%; text-align: right;">${formatMoney(saleData.price.toFixed(2))}</div>
            `;

            document.getElementById('receiptItems').innerHTML = receiptItemsHtml;
            document.getElementById('receiptTotal').textContent = formatMoney(saleData.price.toFixed(2));

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

        function formatDateTime(date = new Date()) {
            const Y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, "0");
            const d = String(date.getDate()).padStart(2, "0");
            const H = String(date.getHours()).padStart(2, "0");
            const i = String(date.getMinutes()).padStart(2, "0");
            const s = String(date.getSeconds()).padStart(2, "0");

            return `${Y}-${m}-${d}, ${H}:${i}:${s}`;
        }

        function numberWithCommas(num) {
            let str = String(num);

            if (str.includes('.')) {
                let [whole, decimal] = str.split('.');

                decimal = decimal.replace(/0+$/, "");

                if (decimal === "") {
                    return Number(whole).toLocaleString();
                }

                let wholeFormatted = Number(whole).toLocaleString();

                return wholeFormatted + "." + decimal;

            } else {
                return Number(str).toLocaleString();
            }
        }

        function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
                const negativeSign = amount < 0 ? "-" : "";
                let i = parseInt(
                    (amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)),
                ).toString();
                let j = i.length > 3 ? i.length % 3 : 0;
                return (
                    negativeSign +
                    (j ? i.substr(0, j) + thousands : "") +
                    i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) +
                    (decimalCount
                        ? decimal +
                        Math.abs(amount - i)
                            .toFixed(decimalCount)
                            .slice(2)
                        : "")
                );
            } catch (e) { }
        }

    </script>
</body>

</html>