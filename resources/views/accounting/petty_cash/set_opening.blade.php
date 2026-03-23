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
                       <input type="text" class="form-control" id="opening_balance" name="opening_balance" value="0.00" placeholder="0.00" oninput="formatAsUserTypes(this)" onblur="formatOnBlur(this)" required>
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
