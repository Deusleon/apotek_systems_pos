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
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                   <div class="form-group">
                       <label for="date">Date</label>
                       <input type="text" class="form-control" id="d_auto_91" name="date" value="<?php echo e(date('Y-m-d')); ?>" required>
                   </div>
                   <div class="form-group">
                       <label for="previous_closing_balance">Previous Day Closing Balance</label>
                       <input type="text" class="form-control" id="previous_closing_balance" name="previous_closing_balance" readonly>
                   </div>
                   <div class="form-group">
                       <label for="opening_balance">Amount Received</label>
                       <input type="text" class="form-control" id="opening_balance" name="opening_balance" value="0" required>
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
<?php /**PATH D:\MY DOCUMENTS\PROJECTS\LARAVEL\APOTEk\Repo-project\apotek_systems_pos\resources\views/accounting/petty_cash/set_opening.blade.php ENDPATH**/ ?>