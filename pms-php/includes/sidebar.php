<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/dashboard/index.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/medicines/list.php">
                    <i class="fas fa-pills"></i> Medicines
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/inventory/stock.php">
                    <i class="fas fa-boxes"></i> Inventory
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/sales/new-sale.php">
                    <i class="fas fa-cash-register"></i> New Sale
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/sales/sales-history.php">
                    <i class="fas fa-history"></i> Sales History
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/purchases/new-purchase.php">
                    <i class="fas fa-shopping-cart"></i> New Purchase
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/purchases/purchase-history.php">
                    <i class="fas fa-file-invoice"></i> Purchase History
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/purchases/suppliers.php">
                    <i class="fas fa-truck"></i> Suppliers
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/customers/list.php">
                    <i class="fas fa-users"></i> Customers
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo APP_URL; ?>/modules/prescriptions/list.php">
                    <i class="fas fa-prescription"></i> Prescriptions
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#reportsMenu">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <div class="collapse" id="reportsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL; ?>/modules/reports/sales-report.php">Sales Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL; ?>/modules/reports/inventory-report.php">Inventory Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL; ?>/modules/reports/financial-report.php">Financial Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL; ?>/modules/reports/expiry-report.php">Expiry Report</a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <?php if (hasRole([ROLE_ADMIN, ROLE_MANAGER])): ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#settingsMenu">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <div class="collapse" id="settingsMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL; ?>/modules/settings/users.php">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL; ?>/modules/settings/system-settings.php">System Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL; ?>/modules/settings/backup.php">Backup</a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
