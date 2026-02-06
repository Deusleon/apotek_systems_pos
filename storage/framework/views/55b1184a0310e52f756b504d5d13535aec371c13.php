<li class="nav-item"><a href="<?php echo e(route('home')); ?>" class="nav-link"><span class="pcoded-micon">
            <i class="fas fa-tachometer-alt"></i></span><span class="pcoded-mtext">Dashboard</span></a>
</li>

<li class="nav-item pcoded-hasmenu">
    <?php if(auth()->user()->checkPermission('View Sales')): ?>
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-money-check-alt"></i></span>
            <span class="pcoded-mtext">Sales</span>
        </a>
        <ul class="pcoded-submenu">
            <?php if(auth()->user()->checkPermission('View Cash Sales')): ?>
                <li class=""><a href="<?php echo e(route('cash-sales.cashSale')); ?>" class="">Cash Sales</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Credit Sales') || auth()->user()->checkPermission('View Credit Tracking') || auth()->user()->checkPermission('View Credit Payments')): ?>
                <li class=""><a href="<?php echo e(route('credit-sales.creditSale')); ?>" class="">Credit Sales</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Delivery Note')): ?>
                <li><a href="<?php echo e(route('delivery-notes.index')); ?>" class="">Delivery Note</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Sales Order') || auth()->user()->checkPermission('View Order List')): ?>
                <li class=""><a href="<?php echo e(route('sale-quotes.index')); ?>" class="">Sales Order</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Sales History')): ?>
                <li class=""><a href="<?php echo e(route('sale-histories.SalesHistory')); ?>" class="">Sales History</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Customers')): ?>
                <li class=""><a href="<?php echo e(route('customers.index')); ?>" class="">Customers</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</li>

<li class="nav-item pcoded-hasmenu">
    <?php if(auth()->user()->checkPermission('View Purchasing')): ?>
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-shopping-cart"></i></span>
            <span class="pcoded-mtext">Purchasing</span>
        </a>
        <ul class="pcoded-submenu">
            <?php if(auth()->user()->checkPermission('View Goods Receiving')): ?>
                <li class=""><a href="<?php echo e(route('goods-receiving.index')); ?>" class="">Goods Receiving</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Purchase Returns')): ?>
                <li class=""><a href="<?php echo e(route('purchase-return.returns')); ?>" class="">Purchase Returns</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Purchase Order')): ?>
                <li class=""><a href="<?php echo e(route('purchase-order.index')); ?>" class="">Purchase Order</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Suppliers')): ?>
                <li class=""><a href="<?php echo e(route('suppliers.index')); ?>" class="">Suppliers</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</li>

<li class="nav-item pcoded-hasmenu">
    <?php if(auth()->user()->checkPermission('View Inventory')): ?>
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-boxes"></i></span>
            <span class="pcoded-mtext">Inventory</span>
        </a>
        <ul class="pcoded-submenu">
            <?php if(auth()->user()->checkPermission('View Product List')): ?>
                <li class=""><a href="<?php echo e(route('products.index')); ?>" class="">Product List</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('Products Import')): ?>
                <li class="nav-item"><a href="<?php echo e(route('import-products')); ?>" class="nav-link"><span class="pcoded-mtext">Products
                            Import</span></a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Current Stock')): ?>
                <li class=""><a href="<?php echo e(route('current-stocks')); ?>" class="">Current Stock</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Details')): ?>
                <li class=""><a href="<?php echo e(route('stock-details')); ?>" class="">Stock Details</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Price List')): ?>
                <li class=""><a href="<?php echo e(route('price-list.index')); ?>" class="">Price List</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Adjustment')): ?>
                <li class=""><a href="<?php echo e(route('new-stock-adjustment')); ?>" class="">Stock Adjustment</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Requisition')): ?>
                <li class=""><a href="<?php echo e(route('requisitions.create')); ?>" class="">Stock Requisition</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Issue')): ?>
                <li class=""><a href="<?php echo e(route('issue.index')); ?>" class="">Stock Issue</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Transfer')): ?>
                <li class=""><a href="<?php echo e(route('stock-transfer.index')); ?>" class="">Stock Transfer</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Count')): ?>
                <li class=""><a href="<?php echo e(route('daily-stock-count.index')); ?>" class="">Stock Count</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Discrepancy Report')): ?>
                <li class=""><a href="<?php echo e(route('stock-discrepancy-report')); ?>" class="">Stock Discrepancy Report</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Batch Stock Count')): ?>
                <li class=""><a href="<?php echo e(route('batch-stock-count.index')); ?>" class="">Batch Stock Count</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Count Schedules')): ?>
                <li class=""><a href="<?php echo e(route('stock-count-schedules.index')); ?>" class="">Stock Count Schedules</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</li>

<li class="nav-item pcoded-hasmenu">
    <?php if(auth()->user()->checkPermission('View Transport')): ?>
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-truck"></i></span>
            <span class="pcoded-mtext">Transport</span>
        </a>
        <ul class="pcoded-submenu">
            <li class=""><a href="<?php echo e(route('transport-logistics.transporters.index')); ?>" class="">Transporters</a></li>
            <li class=""><a href="<?php echo e(route('vehicles.index')); ?>" class="">Vehicles</a></li>
            <li class=""><a href="<?php echo e(route('transport-orders.index')); ?>" class="">Orders</a></li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('payments.index')); ?>">
                    <i class="feather icon-dollar-sign"></i>
                    <span>Payments</span>
                </a>
            </li>
        </ul>
    <?php endif; ?>
</li>

<li class="nav-item pcoded-hasmenu">
    <?php if(auth()->user()->checkPermission('View Accounting')): ?>
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-stream"></i></span>
            <span class="pcoded-mtext">Accounting</span>
        </a>
        <ul class="pcoded-submenu">
            <?php if(auth()->user()->checkPermission('View Petty Cash')): ?>
                <li class="nav-item"><a href="<?php echo e(route('petty-cash.index')); ?>" class="nav-link">
                        <span class="pcoded-mtext">Petty Cash</span></a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Expenses')): ?>
                <li class="nav-item"><a href="<?php echo e(route('expense.index')); ?>" class="nav-link">
                        <span class="pcoded-mtext">Expenses</span></a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Invoices')): ?>
                <li class=""><a href="<?php echo e(route('invoice-management.index')); ?>" class="">Invoices</a></li>
            <?php endif; ?>
            
        </ul>


    <?php endif; ?>
</li>

<li data-username="Vertical Horizontal Box Layout RTL fixed static collapse menu color icon dark"
    class="nav-item pcoded-hasmenu">
    <?php if(auth()->user()->checkPermission('View Reports')): ?>
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-file-pdf"></i></span><span
                class="pcoded-mtext">Reports</span></a>
        <ul class="pcoded-submenu">
            <?php if(auth()->user()->checkPermission('View Sales Reports')): ?>
                <li class=""><a href="<?php echo e(route('sale-report-index')); ?>" class="">Sales Reports</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Purchasing Reports')): ?>
                <li class=""><a href="<?php echo e(route('purchase-report-index')); ?>" class="">Purchasing Reports</a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Inventory Reports')): ?>
                <li class=""><a href="<?php echo e(route('inventory-report-index')); ?>" class="">Inventory Reports</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Accounting Reports')): ?>
                <li class=""><a href="<?php echo e(route('accounting-report-index')); ?>" class="">Accounting Reports</a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Stock Count Analytics')): ?>
                <li class=""><a href="<?php echo e(route('stock-count-analytics')); ?>" class="">Stock Count Analytics</a></li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Transport Reports')): ?>
                <li class=""><a href="<?php echo e(route('transport-report-index')); ?>" class="">Transport Reports</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</li>

<li class="nav-item pcoded-hasmenu">
    <?php if(auth()->user()->checkPermission('View Settings')): ?>
        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-stream"></i></span>
            <span class="pcoded-mtext">Settings</span>
        </a>
        <ul class="pcoded-submenu">
            <?php if(auth()->user()->checkPermission('View General')): ?>
                <li>
                    <a href="#">General</a>
                    <ul class="pcoded-submenu">
                        <?php if(auth()->user()->checkPermission('View Configurations')): ?>
                            <li class="nav-item"><a href="<?php echo e(route('configurations.index')); ?>" class="nav-link">
                                    <span class="pcoded-mtext">Configurations</span></a>
                            </li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Branches')): ?>
                            <li class=""><a href="<?php echo e(route('stores.index')); ?>" class="">Branches</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Product Categories')): ?>
                            <li class=""><a href="<?php echo e(route('product-categories.index')); ?>" class="">Product Categories</a></li>
                        <?php endif; ?>
                        
                        <?php if(auth()->user()->checkPermission('View Price Categories')): ?>
                            <li class=""><a href="<?php echo e(route('price-categories.index')); ?>" class="">Price Categories</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Expense Categories')): ?>
                            <li class=""><a href="<?php echo e(route('expense-categories.index')); ?>" class="">Expense Categories</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Expense Sub Categories')): ?>
                            <li class=""><a href="<?php echo e(route('expense-subcategories.index')); ?>" class="">Expense Subcategories</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Adjustment Reasons')): ?>
                            <li class=""><a href="<?php echo e(route('adjustment-reasons.index')); ?>" class="">Adjustment Reasons</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Terms and Conditions')): ?>
                            <li class="">
                                <a href="<?php echo e(route('general-settings.index')); ?>" class="">Terms and Conditions</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if(auth()->user()->checkPermission('View Security')): ?>
                <li>
                    <a href="#">Security</a>
                    <ul class="pcoded-submenu">
                        <?php if(auth()->user()->checkPermission('View Roles')): ?>
                            <li class=""><a href="<?php echo e(route('roles.index')); ?>" class="">Roles</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Users')): ?>
                            <li class=""><a href="<?php echo e(route('users.index')); ?>" class="">Users</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Activities')): ?>
                            <li class=""><a href="<?php echo e(route('user-activities')); ?>" class="">Activities</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
            
            <?php if(auth()->user()->checkPermission('View Tools')): ?>
                <li>
                    <a href="#">Tools</a>
                    <ul class="pcoded-submenu">
                        <?php if(auth()->user()->checkPermission('View Database Backup')): ?>
                            <li class=""><a href="<?php echo e(route('database-backup.index')); ?>" class="">Database Backup</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Export Stock')): ?>
                            <li class=""><a href="<?php echo e(route('tools.export-stock')); ?>" class="">Export Stock</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Clear Database')): ?>
                            <li class=""><a href="<?php echo e(route('database-clear.index')); ?>" class="">Clear Database</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Reset Stock')): ?>
                            <li class=""><a href="<?php echo e(route('tools.reset-stock-form')); ?>" class="">Reset Stock</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Upload Stock')): ?>
                            <li class=""><a href="<?php echo e(route('tools.upload-stock-form')); ?>" class="">Upload Stock</a></li>
                        <?php endif; ?>
                        <?php if(auth()->user()->checkPermission('View Upload Price')): ?>
                            <li class=""><a href="<?php echo e(route('tools.upload-price-form')); ?>" class="">Upload Price</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>

    <?php endif; ?>
</li>



<li class="nav-item"><a href="<?php echo e(route('mobile-pos.index')); ?>" class="nav-link"><span class="pcoded-micon"><i
                class="fas fa-tablet"></i></span><span class="pcoded-mtext">Waste Collection</span></a></li>
<li class="nav-item"><a href="<?php echo e(route('support.index')); ?>" class="nav-link"><span class="pcoded-micon"><i
                class="fas fa-question"></i></span><span class="pcoded-mtext">Support</span></a>
</li>

</li><?php /**PATH D:\MY DOCUMENTS\PROJECTS\LARAVEL\APOTEk\Repo-project\apotek_systems_pos\resources\views/layouts/menu.blade.php ENDPATH**/ ?>