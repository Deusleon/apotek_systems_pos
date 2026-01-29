@extends("layouts.master")

@section('content-title')
    Sales Order
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Edit Sales Order</a></li>
@endsection

@section('content')
    <style>
        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable,
        .ms-selection {
            background: #fff;
            color: #555555;
            float: left;
            width: 45%;
        }

        #loading {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            display: none;
            opacity: 0.7;
            background-color: #fff;
            z-index: 99;
            text-align: center;
        }

        #loading-image {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 100;
        }

        /* Chrome, Safari, Edge, Opera */
        #cart_table input[type=number]::-webkit-outer-spin-button,
        #cart_table input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        #cart_table input[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

        #cart_table input[type=number] {
            width: 100%;
            box-sizing: border-box;
            padding: 4px 6px;
            height: 36px;
        }
    </style>

    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="new-order" href="{{ route('sale-quotes.index') }}" aria-controls="pills-home"
                    aria-selected="false">New Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="edit-order" href="#" aria-controls="pills-edit" aria-selected="true">Edit
                    Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="order-list" href="{{ route('sale-quotes.order_list') }}"
                    aria-controls="pills-profile" aria-selected="false">Order List</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <form id="edit_proforma_form" name="edit_proforma_form">
                    @if (auth()->user()->checkPermission('Manage Customers'))
                        <div class="row">
                            <div class="col-md-12">
                                <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                                    data-toggle="modal" data-target="#create"> Add New Customer
                                </button>
                            </div>
                        </div>
                    @endif
                    @csrf()
                    <input type="hidden" name="" id="is_all_store" value="{{ current_store()->name }}">
                    <input type="hidden" value="{{ $is_detailed }}" id="is_detailed">
                    <input type="hidden" value="{{ $quote_id }}" id="quote_id">
                    <input type="hidden" value="{{ $vat_rate }}" id="vat_rate">
                    <input type="hidden" value="{{ $fixed_price }}" id="fixed_price">
                    <input type="hidden" value="{{ $vat_rate }}" id="vat">

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label id="cat_label">Sales Type<font color="red">*</font></label>
                                <select id="price_category" class="js-example-basic-single form-control">
                                    <option value="" selected="true" disabled>Select Type</option>
                                    @foreach ($price_category as $price)
                                        <option value="{{ $price->id }}" {{ $price_category_id == $price->id ? 'selected' : '' }}>
                                            {{ $price->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <input type="text" id="quote_barcode_input" autocomplete="off" style="position:absolute; left:-9999px;">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Products<font color="red">*</font></label>
                                <select id="products" class="form-control">
                                    <option value="" disabled selected style="display:none;">Select Product</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="code">Ref #</label>
                                <input type="text" id="ref_no" name="ref_no" class="form-control" value="{{ $ref_no }}" placeholder="Ref #" />
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="code">Customer Name<font color="red">*</font></label>
                                <select id="customer_id" name="customer_id" class="js-example-basic-single form-control" required>
                                    <option value="" disabled>Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" data-vat="{{ $customer->vat }}"
                                            {{ $customer->id == $customer_id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="detail">
                        <hr>
                        <div class="table-responsive" style="width: 100%;">
                            <table id="cart_table" class="table nowrap table-striped table-hover pl-3 pr-3" width="100%">
                            </table>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            @if ($enable_discount === 'YES')
                                <div style="width: 99%">
                                    <label>Discount</label>
                                    <input type="text" id="sale_discount" class="form-control"
                                        value="{{ number_format($discount, 2) }}" />
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="sub_total" class="form-control-plaintext text-md-right" readonly
                                        value="0.00" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total_vat" class="form-control-plaintext text-md-right" readonly
                                        value="0.00" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total Amount:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total" class="form-control-plaintext text-md-right" readonly
                                        value="0.00" />
                                </div>
                                <span class="help-inline text text-danger" style="display: none; margin-left: 63%"
                                    id="discount_error">Invalid Amount</span>
                            </div>
                        </div>

                        <input type="hidden" value="Yes" id="quotes_page">
                        <input type="hidden" value="{{ $enable_discount }}" id="enable_discount">
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-6 d-flex">
                            <div>
                                <b>Total Items:</b>
                                <span id="total_items">0</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group" style="float: right;">
                                <button type="button" class="btn btn-danger" id="cancel_btn">Cancel</button>
                                <button type="button" id="save_btn" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading spinner -->
    <div id="loading">
        <img id="loading-image" src="{{ asset('assets/images/spinner.gif') }}" />
    </div>

    @include('sales.customers.create')
@endsection

@push('page_scripts')
    @include('partials.notification')
    <script type="text/javascript">
        $(document).ready(function() {
            // Focus barcode input
            setTimeout(function() { $('#quote_barcode_input').focus(); }, 150);

            // Global variables
            var cart = [];
            var default_cart = [];
            var cart_table;
            var editingIndex = null;
            
            var is_detailed = document.getElementById('is_detailed').value === 'Detailed';
            var fixed_price = document.getElementById('fixed_price').value || 'NO';
            var vat_rate = parseFloat(document.getElementById('vat_rate').value) || 0;
            var discount_enabled = document.getElementById('enable_discount').value === 'YES';
            var quote_id = document.getElementById('quote_id').value;

            // Get customer VAT status
            var isVATCustomer = $("#customer_id option:selected").data("vat") === 'YES';
            var tax = isVATCustomer ? vat_rate : 0;

            // Initialize cart from existing quote data
            @foreach($sales_details as $item)
            (function() {
                var price = {{ $item->price }};
                var qty = {{ $item->quantity }};
                var vatUnit = Number((price * tax).toFixed(2));
                var unitTotal = Number(price + vatUnit);
                var name = "{{ addslashes($item->name ?? '') }}";
                
                cart.push([
                    name,                          // [0] Name #
                    formatNumber(qty, 0),            // [2] Quantity
                    formatMoney(price),              // [3] Price
                    formatMoney(vatUnit * qty),      // [4] VAT
                    formatMoney(unitTotal * qty),    // [5] Amount
                    0,                               // [6] Stock Qty (not needed for edit)
                    {{ $item->product_id }},         // [7] Product ID
                    '',                              // [8] Product Type
                ]);
                
                default_cart.push([
                    formatMoney(price),
                    formatMoney(vatUnit),
                    formatMoney(unitTotal)
                ]);
            })();
            @endforeach

            // Initialize DataTable for cart
            cart_table = $('#cart_table').DataTable({
                searching: false,
                paging: false,
                info: false,
                ordering: false,
                data: cart,
                columns: [
                    { title: "Name" },
                    { title: "Quantity" },
                    { title: "Price" },
                    { title: "VAT" },
                    { title: "Amount" },
                    { title: "Stock Qty", visible: false },
                    { title: "productID", visible: false },
                    { title: "Product Type", visible: false },
                    {
                        title: "Action",
                        defaultContent: "<div><input type='button' value='Edit' id='edit_btn' class='btn btn-info btn-rounded btn-sm'/><input type='button' value='Delete' id='delete_btn' class='btn btn-danger btn-rounded btn-sm'/></div>"
                    }
                ]
            });

            // Calculate and update totals
            function discount() {
                let sub_total = 0;
                let total_vat = 0;
                let total = 0;

                cart.forEach(function(item) {
                    var quantity = item[1];
                    var price = parseFloat(String(item[2]).replace(/,/g, ''));
                    var vat = parseFloat(String(item[3]).replace(/,/g, ''));
                    var amount = parseFloat(String(item[4]).replace(/,/g, ''));

                    if (typeof quantity === 'string' && quantity.includes('Max')) {
                        quantity = parseFloat(quantity.split(' ')[0].replace(/,/g, ''));
                    } else {
                        quantity = parseFloat(String(quantity).replace(/,/g, ''));
                    }

                    sub_total += (price * quantity);
                    total_vat += vat;
                    total += amount;
                });

                // Apply discount if enabled
                if (discount_enabled) {
                    var dis = document.getElementById("sale_discount").value;
                    var sale_discount = parseFloat(dis.replace(/,/g, ""), 10) || 0;
                    total_vat = (sub_total - sale_discount) * tax;
                    total = (sub_total - sale_discount) + total_vat;
                }

                document.getElementById('sub_total').value = formatMoney(sub_total);
                document.getElementById('total_vat').value = formatMoney(total_vat);
                document.getElementById('total').value = formatMoney(total);
                document.getElementById('total_items').innerHTML = cart.length;

                if (cart_table) {
                    cart_table.clear().rows.add(cart).draw();
                }

                // Disable price category if cart has items
                $('#price_category').prop('disabled', cart.length > 0);
            }

            // Helper functions
            function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
                try {
                    decimalCount = Math.abs(decimalCount);
                    decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
                    const negativeSign = amount < 0 ? "-" : "";
                    let i = parseInt((amount = Math.abs(Number(amount) || 0).toFixed(decimalCount))).toString();
                    let j = i.length > 3 ? i.length % 3 : 0;
                    return (
                        negativeSign +
                        (j ? i.substr(0, j) + thousands : "") +
                        i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) +
                        (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "")
                    );
                } catch (e) {
                    return "0.00";
                }
            }

            function formatNumber(num, decimals) {
                if (isNaN(num)) num = 0;
                return Number(num).toLocaleString(undefined, {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            }

            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            function escapeHtml(text) {
                if (text === null || text === undefined) return '';
                return String(text).replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/'/g, "&#039;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
            }

            // Save edited row
            function saveEdit(index) {
                if (index === null || typeof index === 'undefined' || !cart[index]) return;

                const rowNode = cart_table.row(index).node();
                const $tr = $(rowNode);

                const qtyInput = $tr.find(`#edit_quantity_${index}`);
                const priceInput = $tr.find(`#edit_price_${index}`);

                if (!qtyInput.length && !priceInput.length) {
                    editingIndex = null;
                    return;
                }

                // Read values
                const qtyRaw = qtyInput.length ? qtyInput.val().trim() : String(cart[index][1]).split('<')[0].replace(/,/g, '');
                
                if (!qtyRaw || qtyRaw === '0') {
                    notify('Quantity is required', 'top', 'right', 'warning');
                    qtyInput.focus();
                    return;
                }

                let priceNum;
                if (fixed_price === "NO" && priceInput.length) {
                    if (priceInput.val().trim() === '') {
                        notify('Price is required', 'top', 'right', 'warning');
                        priceInput.focus();
                        return;
                    }
                    priceNum = parseFloat(priceInput.val().replace(/,/g, '')) || 0;
                } else {
                    priceNum = parseFloat(String(cart[index][2]).replace(/,/g, '')) || 0;
                }

                const vatUnit = Number((priceNum * tax).toFixed(2));
                const unitTotal = Number(priceNum + vatUnit);
                const qtyNum = Number(String(qtyRaw).replace(/,/g, '')) || 0;
                
                cart[index][1] = numberWithCommas(qtyNum);
                cart[index][2] = formatMoney(priceNum);
                cart[index][3] = formatMoney(vatUnit * qtyNum);
                cart[index][4] = formatMoney(unitTotal * qtyNum);

                try {
                    cart_table.row(index).data(cart[index]).draw(false);
                } catch (e) {
                    cart_table.clear().rows.add(cart).draw(false);
                }

                discount();
                editingIndex = null;
                setTimeout(function() { $('#quote_barcode_input').focus(); }, 150);
            }

            // Edit button click
            $('#cart_table tbody').on('click', '#edit_btn', function() {
                const $tr = $(this).closest('tr');
                const index = cart_table.row($tr).index();

                if (editingIndex !== index) {
                    editingIndex = index;
                    const rowData = cart[index];

                    const currentName = rowData[0] || '';
                    let currentQty = String(rowData[1] || '1').split('<')[0].replace(/,/g, '');
                    if (!currentQty) currentQty = '1';
                    const currentPrice = (rowData[2]) ? String(rowData[2]).replace(/,/g, '') : '0';

                    $tr.find('td').eq(0).html(escapeHtml(currentName));
                    $tr.find('td').eq(1).html(`<input style="width:100px" type="text" min="1" class="form-control edit_quantity" id="edit_quantity_${index}" value="${escapeHtml(currentQty)}" required onkeypress="return isNumberKey(event,this)">`);
                    if (fixed_price === "NO") {
                        $tr.find('td').eq(2).html(`<input style="width:100px; margin-left:-10%" type="text" class="form-control edit_price" id="edit_price_${index}" value="${escapeHtml(currentPrice)}" required onkeypress="return isNumberKey(event,this)">`);
                    }

                    $tr.find(`#edit_quantity_${index}`).focus();
                    var $editBtn = $("#cart_table tbody tr").eq(index).find("#edit_btn");
                    $editBtn.val('Save');
                } else {
                    saveEdit(index);
                }
            });

            // Blur handler for edit inputs
            $(document).on('focusout', '.edit_quantity, .edit_price', function(e) {
                const id = $(this).attr('id') || '';
                const parts = id.split('_');
                const idx = Number(parts[parts.length - 1]);

                setTimeout(function() {
                    const active = document.activeElement;
                    if (active) {
                        const $active = $(active);
                        if ($active.hasClass('edit_quantity') || $active.hasClass('edit_price')) {
                            const $closestRow = $active.closest('tr');
                            if ($closestRow.length) {
                                const activeRowIndex = cart_table.row($closestRow).index();
                                if (activeRowIndex === idx) {
                                    return;
                                }
                            }
                        }
                    }
                    saveEdit(idx);
                }, 80);
            });

            // Enter key to save edit
            $(document).on('keydown', '.edit_quantity, .edit_price', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const id = $(this).attr('id') || '';
                    const parts = id.split('_');
                    const idx = parts[parts.length - 1];
                    saveEdit(Number(idx));
                }
            });

            // Delete button
            $('#cart_table tbody').on('click', '#delete_btn', function() {
                var index = cart_table.row($(this).parents('tr')).index();
                cart.splice(index, 1);
                default_cart.splice(index, 1);
                discount();
                setTimeout(function() { $('#quote_barcode_input').focus(); }, 150);
            });

            // Number key validation
            window.isNumberKey = function(evt, element) {
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 44) {
                    if (evt.preventDefault) {
                        evt.preventDefault();
                    }
                    return false;
                }
                return true;
            };

            // Add product to cart
            function addProductToCart(productData) {
                let customer_id = document.getElementById("customer_id").value;

                if (!customer_id) {
                    notify('Please select customer first', 'top', 'right', 'warning');
                    return;
                }
                if (!productData) return;

                var sel = document.getElementById("products");
                var productValue = sel.value;
                if (!productValue) return;

                var selectedOption = sel.options[sel.selectedIndex];
                var name = selectedOption.getAttribute("data-name") || selectedOption.text;
                var available_quantity = Number(selectedOption.getAttribute("data-quantity") || 0);
                var productID = productValue;

                let idx = cart.findIndex((r) => r[6] == productID);

                if (idx !== -1) {
                    var price = parseFloat(cart[idx][2].replace(/,/g, ''));
                    var vatUnit = Number((price * tax).toFixed(2));
                    var unitTotal = Number(price + vatUnit);
                    let row = cart[idx];

                    let rawQty = typeof row[1] === "number" ? row[1] : String(row[1]).split("<")[0];
                    rawQty = Number(String(rawQty).replace(/,/g, "")) || 0;

                    let newQty = rawQty + 1;
                    row[1] = numberWithCommas(newQty);
                    row[0] = name;
                    row[2] = formatMoney(price);
                    row[3] = formatMoney(vatUnit * newQty);
                    row[4] = formatMoney(unitTotal * newQty);
                    row[5] = available_quantity;
                    row[6] = productID;

                    cart.splice(idx, 1);
                    cart.unshift(row);

                    if (default_cart && default_cart.length) {
                        const dc = default_cart.splice(idx, 1)[0];
                        default_cart.unshift(dc);
                    }
                } else {
                    var price = Number(selectedOption.getAttribute("data-price") || 0);
                    var vatUnit = Number((price * tax).toFixed(2));
                    var unitTotal = Number(price + vatUnit);
                    
                    var item = [
                        name,
                        1,
                        formatMoney(price),
                        formatMoney(vatUnit),
                        formatMoney(unitTotal),
                        available_quantity,
                        productID,
                        "",
                    ];
                    cart.unshift(item);

                    var cart_data = [
                        formatMoney(price),
                        formatMoney(vatUnit),
                        formatMoney(unitTotal),
                    ];
                    default_cart.unshift(cart_data);
                }

                discount();
                $("#products").val(null).trigger("change");
                $("#quote_barcode_input").focus();
            }

            // Load products based on price category
            function loadProducts() {
                var priceCategory = $('#price_category').val();

                $.ajax({
                    url: '{{ route("selectProducts") }}',
                    type: 'POST',
                    data: {
                        id: priceCategory,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        populateProducts(response.data || []);
                    },
                    error: function(xhr, status, error) {
                        $('#products').empty().append('<option value="">Error loading products</option>');
                    }
                });
            }

            function populateProducts(optionsList) {
                const $sel = $("#products");

                if ($sel.data("select2")) {
                    $sel.select2("destroy");
                }

                $sel.empty();
                $sel.append($("<option>", { value: "", text: "Select product" }));

                if (Array.isArray(optionsList) && optionsList.length) {
                    optionsList.forEach(function(p) {
                        $sel.append(
                            $("<option>", {
                                value: p.id,
                                text: p.name,
                                "data-name": p.name,
                                "data-price": p.price,
                                "data-quantity": p.quantity,
                                "data-barcode": p.barcode,
                                "data-part-no": p.name,
                                "data-xrefs": Array.isArray(p.xrefs) ? p.xrefs.map((x) => x.x_ref_name).join(",") : "",
                            })
                        );
                    });
                }

                function matchCustom(params, data) {
                    if ($.trim(params.term) === "") return data;

                    const term = params.term.toLowerCase();
                    const termNoSpecial = term.replace(/[^a-z0-9]/gi, "");

                    function safeLower(value) {
                        return value !== undefined && value !== null ? String(value).toLowerCase() : "";
                    }

                    function removeSpecialChars(value) {
                        return value.replace(/[^a-z0-9]/gi, "");
                    }

                    const name = safeLower($(data.element).data("name"));
                    const barcode = safeLower($(data.element).data("barcode"));
                    const xrefs = safeLower($(data.element).data("xrefs"));

                    const nameNoSpecial = removeSpecialChars(name);
                    const barcodeNoSpecial = removeSpecialChars(barcode);
                    const xrefsNoSpecial = removeSpecialChars(xrefs);

                    let matchedXref = null;
                    if (xrefs) {
                        const xrefArray = xrefs.split(',');
                        for (let xref of xrefArray) {
                            const xrefTrimmed = xref.trim();
                            const xrefNoSpecial = removeSpecialChars(xrefTrimmed);
                            if (xrefTrimmed.includes(term) || xrefNoSpecial.includes(termNoSpecial)) {
                                matchedXref = xrefTrimmed;
                                break;
                            }
                        }
                    }

                    if (matchedXref) {
                        $(data.element).attr('data-matched-xref', matchedXref);
                    } else {
                        $(data.element).removeAttr('data-matched-xref');
                    }

                    if (
                        name.includes(term) || nameNoSpecial.includes(termNoSpecial) ||
                        barcode.includes(term) || barcodeNoSpecial.includes(termNoSpecial) ||
                        xrefs.includes(term) || xrefsNoSpecial.includes(termNoSpecial)
                    ) {
                        return data;
                    }

                    return null;
                }

                function formatResult(option) {
                    if (!option.id) return option.text;

                    const $option = $(option.element);
                    const partNo = $option.data('part-no') || $option.data('name') || '';
                    const matchedXref = $option.attr('data-matched-xref');

                    let displayText = partNo;

                    return displayText;
                }

                if ($.fn.select2) {
                    $sel.select2({
                        placeholder: "Select Product...",
                        allowClear: true,
                        matcher: matchCustom,
                        templateResult: formatResult,
                        templateSelection: formatResult
                    });
                }

                $sel.val("").trigger("change");
            }

            // Barcode scanner
            $("#quote_barcode_input").on("keypress", function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    let barcode = $(this).val().trim();
                    if (barcode !== "") {
                        fetchProductByBarcode(barcode);
                        $(this).val("");
                    }
                }
            });

            function fetchProductByBarcode(barcode) {
                var price_category = $("#price_category").val();

                $.ajax({
                    url: "{{ route('filter-product-by-word')}}",
                    method: "GET",
                    data: {
                        word: barcode,
                        price_category_id: price_category,
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res && Array.isArray(res.data) && res.data.length > 0) {
                            const prod = res.data[0];
                            addProductToCartScan(prod);
                        } else {
                            notify("Product not found", "top", "right", "danger");
                        }
                    },
                    error: function(err) {
                        notify("Error fetching product", "top", "right", "danger");
                    },
                });
            }

            function addProductToCartScan(product) {
                const priceNum = Number(product.price) || 0;
                const stockQty = Number(product.quantity || product.stock_qty || 0);
                const vatUnit = Number((priceNum * tax).toFixed(2));
                const unitTotal = Number(priceNum + vatUnit);

                let idx = cart.findIndex((r) => String(r[7]) == String(product.id));

                if (idx !== -1) {
                    let row = cart[idx];
                    let existingQtyRaw = String(row[2] || "0").split("<")[0];
                    let existingQty = Number(existingQtyRaw.replace(/,/g, "")) || 0;
                    let newQty = existingQty + 1;

                    row[2] = numberWithCommas(newQty);
                    row[4] = formatMoney(vatUnit * newQty);
                    row[5] = formatMoney(unitTotal * newQty);

                    cart.splice(idx, 1);
                    cart.unshift(row);

                    if (default_cart && default_cart.length && default_cart[idx]) {
                        const dc = default_cart.splice(idx, 1)[0];
                        default_cart.unshift(dc);
                    }
                } else {
                    var name = product.name || product.name || "";

                    var item = [
                        name,
                        1,
                        formatMoney(priceNum),
                        formatMoney(vatUnit),
                        formatMoney(unitTotal),
                        stockQty,
                        product.id,
                        product.type || "",
                    ];

                    var cart_data = [
                        formatMoney(priceNum),
                        formatMoney(vatUnit),
                        formatMoney(unitTotal),
                    ];

                    default_cart.unshift(cart_data);
                    cart.unshift(item);
                }

                discount();
            }

            // Product selection
            $("#products").on('change', function(event) {
                let selectedProduct = $(this).val();
                if (selectedProduct && selectedProduct !== '') {
                    addProductToCart(selectedProduct);
                    setTimeout(() => {
                        $(this).val('').trigger('change');
                    }, 100);
                    setTimeout(function() { $('#quote_barcode_input').focus(); }, 150);
                }
            });

            // Customer change
            $('#customer_id').on('change', function() {
                isVATCustomer = $(this).find('option:selected').data('vat') === 'YES';
                tax = isVATCustomer ? vat_rate : 0;
                
                // Recalculate cart with new tax rate
                cart.forEach(function(item, index) {
                    var price = parseFloat(String(item[3]).replace(/,/g, ''));
                    var qty = parseFloat(String(item[2]).replace(/,/g, ''));
                    var vatUnit = Number((price * tax).toFixed(2));
                    var unitTotal = Number(price + vatUnit);
                    
                    item[4] = formatMoney(vatUnit * qty);
                    item[5] = formatMoney(unitTotal * qty);
                });
                
                discount();
                
                if ($('#price_category').val()) {
                    loadProducts();
                }
                setTimeout(function() { $('#quote_barcode_input').focus(); }, 150);
            });

            // Price category change
            $('#price_category').on('change', function() {
                loadProducts();
            });

            // Discount change
            if (discount_enabled) {
                $("#sale_discount").on("change", function(evt) {
                    if (evt.which != 110) {
                        var n = Math.abs(parseFloat($(this).val().replace(/,/g, ""), 10) || 0);
                        $(this).val(n.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    }
                    discount();
                });

                $("#sale_discount").on("blur", function() {
                    $("#quote_barcode_input").focus();
                });
            }

            // Cancel button
            $('#cancel_btn').on('click', function() {
                if (cart.length > 0) {
                    var r = confirm('Discard all changes and go back?');
                    if (r === true) {
                        window.location.href = '{{ route("sale-quotes.order_list") }}';
                    }
                } else {
                    window.location.href = '{{ route("sale-quotes.order_list") }}';
                }
            });

            // Save button - Save all changes to database
            $('#save_btn').on('click', function(e) {
                e.preventDefault();

                var customer_id = $('#customer_id').val();
                var price_category = $('#price_category').val();
                var ref_no = $('#ref_no').val();
                var sale_discount = 0;

                if (!customer_id) {
                    notify('Please select a customer', 'top', 'right', 'warning');
                    return;
                }

                if (cart.length === 0) {
                    notify('Cannot save empty sales order', 'top', 'right', 'warning');
                    return;
                }

                if (discount_enabled) {
                    sale_discount = parseFloat($('#sale_discount').val().replace(/,/g, '')) || 0;
                }

                // Prepare cart data for saving
                var order_cart = [];
                cart.forEach(function(item) {
                    var quantity = item[1];
                    if (typeof quantity === 'string' && quantity.includes('Max')) {
                        quantity = quantity.split(' ')[0].replace(/,/g, '');
                    } else {
                        quantity = String(quantity).replace(/,/g, '');
                    }

                    var product = {
                        product_id: item[6],
                        quantity: quantity,
                        price: parseFloat(String(item[2]).replace(/,/g, '')),
                        vat: parseFloat(String(item[3]).replace(/,/g, '')),
                        amount: parseFloat(String(item[4]).replace(/,/g, ''))
                    };
                    order_cart.push(product);
                });

                var formData = {
                    quote_id: quote_id,
                    customer_id: customer_id,
                    price_category_id: price_category,
                    ref_no: ref_no,
                    cart: JSON.stringify(order_cart),
                    discount_amount: sale_discount,
                    _token: '{{ csrf_token() }}'
                };

                $('#loading').show();
                $('#save_btn').prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('updateProforma') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        $('#loading').hide();
                        $('#save_btn').prop('disabled', false).text('Save');

                        if (response.status === 'success') {
                            notify('Sales Order updated successfully!', 'top', 'right', 'success');
                            
                            // Redirect to receipt or list
                            if (response.redirect_to) {
                                window.location.href = response.redirect_to;
                            } else {
                                window.location.href = '{{ route("sale-quotes.order_list") }}';
                            }
                        } else {
                            notify(response.message || 'Failed to update sales order', 'top', 'right', 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading').hide();
                        $('#save_btn').prop('disabled', false).text('Save');
                        notify('Error updating sales order: ' + error, 'top', 'right', 'danger');
                    }
                });
            });

            // Initialize
            loadProducts();
            discount();

            // Initialize select2
            $('#customer_id').select2({
                placeholder: 'Select Customer',
                allowClear: false
            });

            $('#price_category').select2({
                placeholder: 'Select Sales Type',
                allowClear: false
            });

            // Make discount function available globally
            window.discount = discount;
        });
    </script>
    <script src="{{ asset('assets/apotek/js/notification.js') }}"></script>
    <script src="{{ asset('assets/apotek/js/customer.js') }}"></script>
@endpush
