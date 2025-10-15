<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Surgery Operating Room Scheduling</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/sors.js" defer></script>
</head>
<body>
    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="brand">
                <span class="brand-name">CAREOPS</span>
                <span class="brand-module">SORS</span>
            </div>
        </div>
        <div class="header-right">
            <a href="/id-login-admin/dashboard.php" class="btn btn-primary btn-sm">🏠 Main Dashboard</a>
            <span class="user-name"><i class="fas fa-user-circle"></i> <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User'; ?></span>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a href="index.php?route=dashboard" class="nav-item <?php echo (!isset($_GET['route']) || $_GET['route'] === 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="index.php?route=surgeries/list" class="nav-item <?php echo (isset($_GET['route']) && strpos($_GET['route'], 'surgeries') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-procedures"></i>
                <span>Surgeries</span>
            </a>
            <a href="index.php?route=surgeries/calendar" class="nav-item <?php echo (isset($_GET['route']) && $_GET['route'] === 'surgeries/calendar') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendar</span>
            </a>
            <a href="index.php?route=rooms/list" class="nav-item <?php echo (isset($_GET['route']) && strpos($_GET['route'], 'rooms') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-door-open"></i>
                <span>Operating Rooms</span>
            </a>
            <a href="index.php?route=staff/list" class="nav-item <?php echo (isset($_GET['route']) && strpos($_GET['route'], 'staff') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-user-md"></i>
                <span>Staff</span>
            </a>
            <a href="index.php?route=reports/list" class="nav-item <?php echo (isset($_GET['route']) && strpos($_GET['route'], 'reports') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page content will be inserted here -->
    </main>

    <?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
