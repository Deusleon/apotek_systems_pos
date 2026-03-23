<!-- Add Expense Modal -->
<div class="modal fade" id="add-expense" tabindex="-1" role="dialog" aria-labelledby="add-expense-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add-expense-label">Add Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add-expense-form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="petty_cash_id" name="petty_cash_id">
                    <div class="form-group">
                        <label for="details">Details</label>
                        <input type="text" class="form-control" id="details" name="details" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount Spent</label>
                        <input type="text" class="form-control" id="amount" name="amount" placeholder="0.00" oninput="formatAsUserTypes(this)" onblur="formatOnBlur(this)" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="add-expense-submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
