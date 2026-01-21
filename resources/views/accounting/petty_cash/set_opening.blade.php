<!-- Set Opening Balance Modal -->
<div class="modal fade" id="set-opening" tabindex="-1" role="dialog" aria-labelledby="set-opening-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="set-opening-label">Set Opening Balance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="set-opening-form">
                @csrf
                <div class="modal-body">
                   <div class="form-group">
                       <label for="date">Date</label>
                       <input type="text" class="form-control" id="d_auto_91" name="date" value="{{ date('Y-m-d') }}" required>
                   </div>
                   <div class="form-group">
                       <label for="previous_closing_balance">Previous Day Closing Balance</label>
                       <input type="text" class="form-control" id="previous_closing_balance" name="previous_closing_balance" readonly>
                   </div>
                   <div class="form-group">
                       <label for="opening_balance">Amount Received</label>
                       <input type="number" step="0.01" class="form-control" id="opening_balance" name="opening_balance" value="0" required>
                   </div>
               </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="set-opening-submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Ensure jQuery is loaded before executing any jQuery-dependent code
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded. Please check script loading order.');
} else {
    console.log('jQuery is available:', typeof $);
    
    $(document).ready(function() {
        console.log('Set opening modal JavaScript loaded');
        
        // Initialize date picker
        var start = moment();
        var end = moment();

        $('#d_auto_91').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            maxDate: end,
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
    });

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

    // Format money function
    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
        try {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
            const negativeSign = amount < 0 ? "-" : "";
            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;
            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {
            console.log(e)
        }
    }
}
</script>