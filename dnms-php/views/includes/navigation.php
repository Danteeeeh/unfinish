<?php
// Get current user info if logged in
$currentUser = [];
if (isset($_SESSION['user_id'])) {
    try {
        // Get database connection if available
        if (function_exists('getDNMSDBConnection')) {
            $db = getDNMSDBConnection();
            $userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $userStmt->execute([$_SESSION['user_id']]);
            $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // Handle error silently
    }
}
?>

<!-- Top Header -->
<nav class="top-header">
    <div class="header-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="brand">
            <a href="../index.php" class="brand-link">
                <i class="fas fa-leaf"></i>
                <span class="brand-name">NutriTrack Pro</span>
            </a>
        </div>
    </div>
    <div class="header-right">
        <div class="user-name">
            <i class="fas fa-user"></i>
            <span><?php echo htmlspecialchars($currentUser['full_name'] ?? 'Guest'); ?></span>
        </div>
        <a href="../../../logout.php" class="btn btn-secondary btn-sm">Logout</a>
    </div>
</nav>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <a href="../../index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="../../views/food/search.php" class="nav-item">
            <i class="fas fa-search"></i>
            <span>Food Search</span>
        </a>
        <a href="../../controllers/meal/planner.php" class="nav-item">
            <i class="fas fa-calendar-alt"></i>
            <span>Meal Planner</span>
        </a>
        <a href="../../controllers/meal/log.php" class="nav-item">
            <i class="fas fa-book"></i>
            <span>Meal Log</span>
        </a>
        <a href="../../controllers/meal/history.php" class="nav-item">
            <i class="fas fa-history"></i>
            <span>Meal History</span>
        </a>
        <a href="../../views/goals/index.php" class="nav-item">
            <i class="fas fa-bullseye"></i>
            <span>Goals</span>
        </a>
        <a href="../../views/reports/index.php" class="nav-item">
            <i class="fas fa-chart-line"></i>
            <span>Reports</span>
        </a>
    </nav>
</aside>

<!-- Main Content Wrapper -->
<main class="main-content" id="mainContent">
    <div class="dashboard-page">
        <div class="content">
            <!-- Page content will be inserted here -->

        </div>
    </div>
</main>

<!-- Sidebar Toggle Script -->
<script>
    // Sidebar Toggle with proper state management
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleButton = document.querySelector('.menu-toggle i');

        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');

        // Update toggle icon
        if (sidebar.classList.contains('collapsed')) {
            toggleButton.classList.remove('fa-bars');
            toggleButton.classList.add('fa-times');
        } else {
            toggleButton.classList.remove('fa-times');
            toggleButton.classList.add('fa-bars');
        }
    }

    // Close sidebar when clicking outside (mobile)
    function closeSidebarOnOutsideClick() {
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.querySelector('.menu-toggle');

            if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
                if (!sidebar.classList.contains('collapsed')) {
                    toggleSidebar();
                }
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        closeSidebarOnOutsideClick();

        // Add smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add keyboard navigation for sidebar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar.classList.contains('collapsed')) {
                    toggleSidebar();
                }
            }
        });
    });
</script>
