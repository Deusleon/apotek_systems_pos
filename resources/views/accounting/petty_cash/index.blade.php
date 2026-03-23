@extends("layouts.master")

@section('page_css')
<style>
    .datepicker>.datepicker-days {
        display: block;
    }

    ol.linenums {
        margin: 0 0 0 -8px;
    }

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

    input[type=button]:focus {
        background-color: #748892;
        border-color: #748892;
        color: white;
    }
</style>
@endsection

@section('content-title')
Petty Cash
@endsection

@section('content-sub-title')
<li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
<li class="breadcrumb-item"><a href="#">Accounting / Petty Cash </a></li>
@endsection

@section("content")

<div class="col-sm-12">
    <div class="card">
        <div class="card-body">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                <div class="row">
                    <div class="col-md-9">
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            @if(auth()->user()->checkPermission('Add Petty Cash'))
                                <button style="float: right;margin-bottom: 7%;" type="button"
                                    class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#set-opening">
                                    Set Opening Balance
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mb-3 align-items-center">
                    <label class="mr-2" for="">Date:</label>
                    <input type="text" name="date_of_petty_cash" id="petty_cash_date" class="form-control w-auto">
                </div>

                {{--ajax loading gif--}}
                <div id="loading">
                    <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                </div>

                <div id="tbody-header-petty-cash" class="table-responsive">
                    <table id="fixed-header-petty-cash" class="display table nowrap table-striped table-hover"
                        style="width:100%;">

                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Opening Balance</th>
                                <th>Amount</th>
                                <th>Expenses</th>
                                <th>Closing Balance</th>
                                <th>Debts</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@include("accounting.petty_cash.set_opening")
@include("accounting.petty_cash.add_expense")
@include("accounting.petty_cash.show_expenses")

<script>
function formatAsUserTypes(input) {
    // Get the raw value (remove commas for processing)
    let value = input.value.replace(/,/g, '');
    
    // Only allow numbers and decimal point
    value = value.replace(/[^0-9.]/g, '');
    
    // Prevent multiple decimal points
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts[1];
    }
    
    // Format with commas as user types (no decimal yet)
    if (value) {
        const integerPart = value.split('.')[0];
        const formatted = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        input.value = value.includes('.') ? formatted + '.' + value.split('.')[1] : formatted;
    } else {
        input.value = '';
    }
}

function formatOnBlur(input) {
    // Get the raw value
    let value = input.value.replace(/,/g, '');
    
    // Parse as float
    let num = parseFloat(value);
    
    // If valid number, format with 2 decimal places
    if (!isNaN(num)) {
        input.value = num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else {
        input.value = '0.00';
    }
}
</script>

@endsection

@push("page_scripts")
<script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
<script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
@include('partials.notification')

<script>
// Ensure jQuery is available before using it
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded. Please check script loading order.');
} else {
    console.log('jQuery is available:', typeof $);
}
$(document).ready(function() {
console.log('Petty cash page ready, jQuery available:', typeof $);

// Initialize date picker for set opening modal
$('#d_auto_91').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    maxDate: moment(),
    autoUpdateInput: true,
    locale: {
        format: 'YYYY-MM-DD'
    }
});

// Set default value to today's date when modal is shown
$('#set-opening').on('shown.bs.modal', function() {
    var today = moment().format('YYYY-MM-DD');
    $('#d_auto_91').val(today);
    updatePreviousClosingBalance(today);
});

// Update previous closing balance when date changes
$('#d_auto_91').on('change', function() {
    var selectedDate = $(this).val();
    if (selectedDate) {
        updatePreviousClosingBalance(selectedDate);
    }
});
/*petty cash filter table results*/
console.log('Initializing petty cash DataTable');
var hasAddPermission = @json(auth()->user()->checkPermission('Add Petty Cash'));
console.log('User has Add Petty Cash permission:', hasAddPermission);

// Function to generate action buttons based on record date
function generateActionButtons(recordDate) {
    var buttons = `<button class="btn btn-success btn-rounded btn-sm" type="button" id="show_expenses_btn">Show</button>`;

    if (hasAddPermission) {
        // Only show Add Expense button for current day and future dates
        var recordDateObj = new Date(recordDate);
        var today = new Date();
        today.setHours(0, 0, 0, 0); // Set to start of day

        if (recordDateObj >= today) {
            buttons += ` <button class="btn btn-info btn-rounded btn-sm" type="button" id="add_expense_btn">Add</button>`;
        }
    }

    return buttons;
}

var table_petty_cash_filter = $('#fixed-header-petty-cash').DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    'columns': [
        {
            'data': 'date',
            render: function (data, type, row) {
                return data;
            }
        },
        {
            'data': 'opening_balance',
            render: function (amount) {
                return formatMoney(amount);
            }
        },
        {
            'data': 'amount_received',
            render: function (amount) {
                return formatMoney(amount);
            }
        },
        {
            'data': 'expenses_total',
            render: function (amount) {
                return formatMoney(amount);
            }
        },
        {
            'data': 'closing_balance',
            render: function (amount) {
                return formatMoney(amount);
            }
        },
        {
            'data': 'debts',
            render: function (amount) {
                return formatMoney(amount);
            }
        },
        {
            data: 'action',
            render: function(data, type, row) {
                return generateActionButtons(row.date);
            }
        }


    ], aaSorting: [[0, "desc"]]

});

$(document).on('click', '#add_expense_btn', function () {
    var row_data = table_petty_cash_filter.row($(this).parents('tr')).data();
    console.log('Add expense button clicked for record:', row_data);

    // Check if the record date is in the past
    var recordDate = new Date(row_data.date);
    var today = new Date();
    today.setHours(0, 0, 0, 0); // Set to start of day for comparison

    if (recordDate < today) {
        notify('Cannot add expenses to past dates. Only current day and future dates allowed.', 'top', 'right', 'warning');
        return;
    }

    $('#add-expense').find('.modal-body #petty_cash_id').val(row_data.id);
    $('#add-expense').modal('show');
});

$(document).on('click', '#show_expenses_btn', function () {
    var row_data = table_petty_cash_filter.row($(this).parents('tr')).data();
    console.log('Show expenses button clicked for record:', row_data);
    // Load expenses for this petty cash record
    loadExpenses(row_data.id);
    $('#show-expenses').modal('show');
});

// Handle set opening balance button
$(document).on('click', '#set-opening-submit', function(e) {
    e.preventDefault();
    console.log('Set opening button clicked from index');
    var formData = new FormData($('#set-opening-form')[0]);

    $.ajax({
        url: '{{ route("petty-cash.store") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Set opening success:', response);
            $('#set-opening').modal('hide');
            $('#set-opening-form')[0].reset();
            getPettyCashDate(); // Refresh table
            notify('Petty cash opening balance set successfully!', 'top', 'right', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Set opening error:', xhr.responseText, status, error);
            var errorMsg = 'Error setting opening balance';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            notify(errorMsg, 'top', 'right', 'danger');
        }
    });
});

// Handle add expense button
$(document).on('click', '#add-expense-submit', function(e) {
    e.preventDefault();
    console.log('Add expense button clicked from index');
    var formData = new FormData($('#add-expense-form')[0]);
    var pettyCashId = $('#petty_cash_id').val();

    $.ajax({
        url: '{{ url("petty-cash") }}/' + pettyCashId + '/add-expense',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Add expense success:', response);
            $('#add-expense').modal('hide');
            $('#add-expense-form')[0].reset();
            getPettyCashDate(); // Refresh table
            notify('Expense added successfully!', 'top', 'right', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Add expense error:', xhr.responseText, status, error);
            var errorMsg = 'Error adding expense';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            notify(errorMsg, 'top', 'right', 'danger');
        }
    });
});

$(function () {
    var start = moment().startOf('month');
    var end = moment().endOf('month');

    function cb(start, end) {
        $('#petty_cash_date').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
        console.log('Date range set to:', start.format('YYYY/MM/DD'), 'to', end.format('YYYY/MM/DD'));
        getPettyCashDate();
    }

    $('#petty_cash_date').daterangepicker({
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        maxDate: end,
        autoUpdateInput: true,
        locale: {
            format: 'YYYY/MM/DD'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf('year'), moment()]
        }
    }, cb);

    // Initialize the date range picker and trigger data load
    cb(start, end);

    $('#petty_cash_date').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        console.log('Date range applied:', picker.startDate.format('YYYY/MM/DD'), 'to', picker.endDate.format('YYYY/MM/DD'));
        getPettyCashDate();
    });

    // Ensure the table is initialized and data is loaded on page load
    setTimeout(function() {
        if ($('#fixed-header-petty-cash').DataTable().rows().count() === 0) {
            console.log('Table is empty, triggering data load');
            getPettyCashDate();
        }
    }, 500);

});

/* Get petty cash date range and call AJAX */
function getPettyCashDate() {
    var value = $('#petty_cash_date').val();
    if (!value) return;

    var dates = value.split('-').map(d => d.trim());
    filterPettyCashDate(dates);
}

/* AJAX request to backend */
function filterPettyCashDate(dates) {
    var from_date = moment(dates[0], 'YYYY/MM/DD').format('YYYY-MM-DD');
    var to_date = moment(dates[1], 'YYYY/MM/DD').format('YYYY-MM-DD');

    console.log('Filtering petty cash from:', from_date, 'to:', to_date);

    $('#loading').show();

    $.ajax({
        url: '{{ route("petty-cash.filter-by-date") }}',
        type: 'GET',
        dataType: 'json',
        data: {
            from_date: from_date,
            to_date: to_date
        },
        success: function (data) {
            console.log('Petty cash data loaded:', data);
            bindPettyCashData(data);
        },
        error: function(xhr, status, error) {
            console.error('Petty cash AJAX error:', xhr.responseText, status, error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to load petty cash data');
            }
        },
        complete: function () {
            $('#loading').hide();
        }
    });
}

/* Bind filtered data to DataTable */
function bindPettyCashData(data) {
    console.log('Binding petty cash data:', data);
    console.log('Number of records received:', data.length);
    
    if (data.length === 0) {
        console.log('No data received for the selected date range');
    }
    
    table_petty_cash_filter.clear();
    table_petty_cash_filter.rows.add(data);
    table_petty_cash_filter.draw();
    console.log('DataTable draw complete');
}

/* Load expenses for a petty cash record */
function loadExpenses(pettyCashId) {
    $.ajax({
        url: '{{ url("petty-cash") }}/' + pettyCashId + '/expenses',
        type: 'GET',
        success: function (data) {
            var html = '';
            data.forEach(function(expense) {
                html += '<tr>';
                html += '<td>' + expense.details + '</td>';
                html += '<td>' + formatMoney(expense.amount) + '</td>';
                html += '<td>' + expense.created_at.split(' ')[0] + '</td>';
                html += '</tr>';
            });
            $('#expenses-table-body').html(html);
        }
    });
}

/*format money*/
function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
    try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
        const negativeSign = amount < 0 ? "-" : "";
        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;
        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
    }
}

/* Update previous closing balance */
function updatePreviousClosingBalance(date) {
    $.ajax({
        url: '{{ route("petty-cash.previous-closing") }}',
        type: 'GET',
        data: {
            date: date
        },
        success: function(response) {
            if (response.closing_balance !== undefined) {
                $('#previous_closing_balance').val(formatMoney(response.closing_balance));
            } else {
                $('#previous_closing_balance').val('0.00');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching previous closing balance:', error);
            $('#previous_closing_balance').val('0.00');
        }
    });
}

// Amount formatting for add expense modal
$('#amount').on('blur', function() {
    var value = $(this).val().replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        var number = parseFloat(value);
        $(this).val(number.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    } else {
        $(this).val('0.00');
    }
});

$('#amount').on('focus', function() {
    var value = $(this).val().replace(/,/g, '');
    $(this).val(value);
});

// Clean the value on form submit to send numeric value
$('#add-expense-form').on('submit', function() {
    var amount = $('#amount').val().replace(/,/g, '');
    $('#amount').val(amount);
});

// Also clean on button click in case it's AJAX
$('#add-expense-submit').on('click', function() {
    var amount = $('#amount').val().replace(/,/g, '');
    $('#amount').val(amount);
});

// Amount formatting for set opening balance modal
$('#opening_balance').on('blur', function() {
    var value = $(this).val().replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        var number = parseFloat(value);
        $(this).val(number.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    } else {
        $(this).val('0.00');
    }
});

$('#opening_balance').on('focus', function() {
    var value = $(this).val().replace(/,/g, '');
    $(this).val(value);
});

// Clean the value on form submit to send numeric value
$('#set-opening-form').on('submit', function() {
    var amount = $('#opening_balance').val().replace(/,/g, '');
    $('#opening_balance').val(amount);
});

// Also clean on button click in case it's AJAX
$('#set-opening-submit').on('click', function() {
    var amount = $('#opening_balance').val().replace(/,/g, '');
    $('#opening_balance').val(amount);
});

});

</script>

@endpush