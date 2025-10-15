<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-bars"></i> Menu</h3>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="index.php?route=dashboard" class="<?php echo (!isset($_GET['route']) || $_GET['route'] === 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="index.php?route=patients/list" class="<?php echo (isset($_GET['route']) && strpos($_GET['route'], 'patients') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-user-injured"></i> Patient Management
            </a>
        </li>
        <li>
            <a href="index.php?route=samples/list" class="<?php echo (isset($_GET['route']) && strpos($_GET['route'], 'samples') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-vial"></i> Sample Tracking
            </a>
        </li>
        <li>
            <a href="index.php?route=tests/pending" class="<?php echo (isset($_GET['route']) && strpos($_GET['route'], 'tests') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-microscope"></i> Test Processing
            </a>
        </li>
        <li>
            <a href="index.php?route=reports/list" class="<?php echo (isset($_GET['route']) && strpos($_GET['route'], 'reports') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-file-medical"></i> Reports
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <div>
                <strong><?php echo getCurrentUser()['full_name']; ?></strong>
                <small><?php echo ucfirst(getCurrentUser()['role']); ?></small>
            </div>
        </div>
    </div>
</aside>

<style>
    /* Enhanced Sidebar Responsiveness */
    .sidebar {
        position: fixed;
        left: 0;
        top: 80px;
        height: calc(100vh - 80px);
        overflow-y: auto;
        border-right: 1px solid var(--border-color);
        transition: transform 0.3s ease, background-color 0.3s ease;
        z-index: 999;
        width: 280px;
    }

    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-secondary);
    }

    .sidebar-header h3 {
        margin: 0;
        color: var(--text-primary);
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sidebar-menu {
        list-style: none;
        padding: 1rem 0;
        margin: 0;
    }

    .sidebar-menu li {
        margin: 0;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }

    .sidebar-menu a:hover {
        background: rgba(102, 126, 234, 0.1);
        color: var(--text-primary);
    }

    .sidebar-menu a.active {
        background: var(--lab-gradient);
        color: white;
    }

    .sidebar-menu a i {
        width: 20px;
        text-align: center;
    }

    .sidebar-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1rem;
        border-top: 1px solid var(--border-color);
        background: var(--bg-secondary);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-primary);
    }

    .user-info i {
        font-size: 1.5rem;
        color: var(--text-secondary);
    }

    .user-info div {
        display: flex;
        flex-direction: column;
    }

    .user-info strong {
        font-size: 0.875rem;
    }

    .user-info small {
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 250px;
            z-index: 1001;
        }

        .sidebar-header h3 {
            font-size: 1rem;
        }

        .sidebar-menu a {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }
    }
</style>
