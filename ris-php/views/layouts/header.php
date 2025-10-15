<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Radiology Information System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="css/forms.css">
    <style>
        /* RIS Enhanced Styling */
        :root {
            --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            --secondary-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --ris-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);

            /* Light Theme Variables */
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-muted: #868e96;
            --border-color: #dee2e6;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            --card-bg: #ffffff;
            --navbar-bg: rgba(255, 255, 255, 0.95);
            --sidebar-bg: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Ensure proper layering */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Fix for navigation issues */
        .nav-item, .btn {
            position: relative;
            z-index: 1;
        }

        /* Ensure links work properly */
        a.nav-item, a.btn {
            display: block;
            text-decoration: none;
            color: inherit;
        }

        /* Fix for button clicks */
        .btn {
            border: none;
            background: none;
            font-family: inherit;
            cursor: pointer;
        }

        /* Fix potential z-index conflicts */
        .sidebar {
            z-index: 1000 !important;
        }

        .sidebar.show {
            z-index: 1001 !important;
        }

        /* Ensure navigation elements are clickable */
        .nav-item, .btn {
            pointer-events: auto !important;
            user-select: none;
        }

        /* Fix for any overlay issues */
        body {
            position: relative;
        }

        /* Ensure proper stacking context */
        .top-header {
            position: relative;
            z-index: 999;
        }

        /* Top Header */
        .top-header {
            background: var(--navbar-bg);
            backdrop-filter: blur(20px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
            box-shadow: var(--shadow-sm);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .menu-toggle {
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .menu-toggle:hover {
            background: var(--border-color);
            transform: scale(1.05);
        }

        .brand {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-name {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--ris-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-module {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--ris-gradient);
            color: white;
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: var(--border-color);
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .user-name {
            color: var(--text-secondary);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 80px;
            height: calc(100vh - 80px);
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
            transition: transform 0.3s ease;
            z-index: 999;
            transform: translateX(0);
        }

        .sidebar-nav {
            padding: 2rem 1rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 0.5rem;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--ris-gradient);
            transition: left 0.3s ease;
            z-index: -1;
            opacity: 0.1;
        }

        .nav-item:hover::before {
            left: 0;
        }

        .nav-item:hover {
            color: var(--text-primary);
            background: rgba(37, 99, 235, 0.1);
            transform: translateX(8px);
        }

        .nav-item.active {
            background: var(--ris-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transform: translateX(4px);
        }

        .nav-item i {
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        /* Navigation Sections */
        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1.25rem;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .nav-section-title i {
            font-size: 1.1rem;
            color: var(--primary-gradient);
        }

        .nav-item.sub-item {
            padding: 0.75rem 1.25rem 0.75rem 3rem;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
        }

        .nav-item.sub-item:hover {
            border-left-color: var(--primary-gradient);
            background: rgba(37, 99, 235, 0.05);
        }

        .nav-item.sub-item.active {
            border-left-color: var(--primary-gradient);
            background: rgba(37, 99, 235, 0.1);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 80px;
            padding: 2rem;
            min-height: calc(100vh - 80px);
            background: var(--bg-primary);
            transition: margin-left 0.3s ease;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .top-header {
                padding: 1rem;
            }

            .user-name {
                display: none;
            }

            .main-content {
                padding: 1rem;
            }

            .sidebar {
                width: 100%;
                z-index: 1000;
            }
        }
    </style>
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
                <span class="brand-module">RIS</span>
            </div>
        </div>
        <div class="header-right">
            <a href="../../../dashboard.php" class="btn btn-primary btn-sm" title="Go to Main Dashboard">
                <i class="fas fa-home"></i>
                Main Dashboard
            </a>
            <a href="../../../logout.php" class="btn btn-secondary btn-sm" title="Logout from System">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
            <span class="user-name">
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
            </span>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a href="index.php?route=dashboard" class="nav-item active" title="RIS Dashboard - Overview and Statistics">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <!-- Patient Management -->
            <div class="nav-section">
                <div class="nav-section-title">
                    <i class="fas fa-user-injured"></i>
                    <span>Patients</span>
                </div>
                <a href="index.php?route=patients/list" class="nav-item sub-item">
                    <i class="fas fa-list"></i>
                    <span>All Patients</span>
                </a>
                <a href="index.php?route=patients/add" class="nav-item sub-item">
                    <i class="fas fa-plus"></i>
                    <span>Add Patient</span>
                </a>
                <a href="index.php?route=patients/search" class="nav-item sub-item">
                    <i class="fas fa-search"></i>
                    <span>Search Patient</span>
                </a>
            </div>

            <!-- Study Management -->
            <div class="nav-section">
                <div class="nav-section-title">
                    <i class="fas fa-x-ray"></i>
                    <span>Studies</span>
                </div>
                <a href="index.php?route=studies/list" class="nav-item sub-item">
                    <i class="fas fa-list"></i>
                    <span>All Studies</span>
                </a>
                <a href="index.php?route=studies/add" class="nav-item sub-item">
                    <i class="fas fa-plus"></i>
                    <span>Add Study</span>
                </a>
                <a href="index.php?route=studies/worklist" class="nav-item sub-item">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Worklist</span>
                </a>
            </div>

            <!-- Report Management -->
            <div class="nav-section">
                <div class="nav-section-title">
                    <i class="fas fa-file-medical"></i>
                    <span>Reports</span>
                </div>
                <a href="index.php?route=reports/list" class="nav-item sub-item">
                    <i class="fas fa-list"></i>
                    <span>All Reports</span>
                </a>
                <a href="index.php?route=reports/create" class="nav-item sub-item">
                    <i class="fas fa-plus"></i>
                    <span>Create Report</span>
                </a>
            </div>

            <!-- User Management -->
            <div class="nav-section">
                <div class="nav-section-title">
                    <i class="fas fa-users-cog"></i>
                    <span>Users</span>
                </div>
                <a href="index.php?route=users/list" class="nav-item sub-item">
                    <i class="fas fa-list"></i>
                    <span>All Users</span>
                </a>
                <a href="index.php?route=users/add" class="nav-item sub-item">
                    <i class="fas fa-plus"></i>
                    <span>Add User</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">

    <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
    <nav class="breadcrumb">
        <a href="index.php?route=dashboard" class="breadcrumb-item">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <?php foreach ($breadcrumbs as $breadcrumb): ?>
        <span class="breadcrumb-separator">/</span>
        <?php if (isset($breadcrumb['url'])): ?>
        <a href="<?php echo $breadcrumb['url']; ?>" class="breadcrumb-item">
            <?php echo htmlspecialchars($breadcrumb['title']); ?>
        </a>
        <?php else: ?>
        <span class="breadcrumb-item active">
            <?php echo htmlspecialchars($breadcrumb['title']); ?>
        </span>
        <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');

            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });
            }

            // Function to update active navigation state
            function updateActiveNavigation() {
                try {
                    const currentUrl = new URL(window.location.href);
                    const currentPath = currentUrl.pathname;
                    const currentRoute = currentUrl.searchParams.get('route') || 'dashboard';

                    console.log('Current URL:', currentUrl.href);
                    console.log('Current route:', currentRoute);

                    // Remove active class from all nav items
                    document.querySelectorAll('.nav-item').forEach(item => {
                        item.classList.remove('active');
                    });

                    // Handle different URL patterns
                    if (currentPath.includes('dashboard.php') || currentRoute === 'dashboard') {
                        // Dashboard is active
                        const dashboardLinks = document.querySelectorAll('a[href*="dashboard"], a[href*="route=dashboard"]');
                        dashboardLinks.forEach(link => {
                            link.classList.add('active');
                        });
                        console.log('Dashboard activated');
                    } else {
                        // Find matching nav item based on route
                        document.querySelectorAll('.nav-item').forEach(item => {
                            const href = item.getAttribute('href');
                            if (href) {
                                try {
                                    const url = new URL(href, window.location.origin);
                                    const route = url.searchParams.get('route');
                                    if (route === currentRoute) {
                                        item.classList.add('active');
                                        console.log('Activated route:', route);
                                    }
                                } catch (e) {
                                    console.warn('Invalid navigation URL:', href, e.message);
                                }
                            }
                        });
                    }
                } catch (error) {
                    console.error('Error updating active navigation:', error);
                }
            }

            // Ensure proper initialization after DOM is ready
            function initializeRISNavigation() {
                console.log('🔄 Initializing RIS Navigation System...');

                // Verify all navigation elements exist
                const requiredElements = {
                    sidebar: document.getElementById('sidebar'),
                    menuToggle: document.getElementById('menuToggle'),
                    navItems: document.querySelectorAll('.nav-item'),
                    headerButtons: document.querySelectorAll('.header-right .btn')
                };

                // Check for missing elements
                Object.entries(requiredElements).forEach(([name, elements]) => {
                    if (name === 'navItems' || name === 'headerButtons') {
                        console.log(`✅ ${name}: ${elements.length} elements found`);
                    } else if (elements) {
                        console.log(`✅ ${name}: Element exists`);
                    } else {
                        console.warn(`❌ ${name}: Element missing`);
                    }
                });

                // Test route connectivity
                const testRoutes = [
                    'dashboard',
                    'patients/list', 'patients/add', 'patients/search',
                    'studies/list', 'studies/add', 'studies/worklist',
                    'reports/list', 'reports/create',
                    'users/list', 'users/add'
                ];

                console.log('🔗 Testing route connectivity...');
                testRoutes.forEach(route => {
                    const testUrl = `index.php?route=${route}`;
                    const fullUrl = new URL(testUrl, window.location.origin);

                    // Test if route resolves properly
                    fetch(testUrl, {
                        method: 'HEAD',
                        mode: 'no-cors'
                    }).then(() => {
                        console.log(`✅ Route ${route} accessible`);
                    }).catch(() => {
                        console.warn(`❌ Route ${route} may have issues`);
                    });
                });

                // Ensure responsive behavior is working
                function handleResponsiveChanges() {
                    const sidebar = requiredElements.sidebar;
                    if (sidebar && window.innerWidth <= 1024) {
                        sidebar.classList.remove('show');
                        console.log('📱 Mobile view: Sidebar hidden by default');
                    }
                }

                // Initial responsive setup
                handleResponsiveChanges();

                // Re-check on orientation change
                window.addEventListener('orientationchange', handleResponsiveChanges);

                console.log('🎯 RIS Navigation initialization complete');
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeRISNavigation);
            } else {
                initializeRISNavigation();
            }

            // Handle window resize for responsive behavior
            window.addEventListener('resize', function() {
                const sidebar = document.getElementById('sidebar');
                if (window.innerWidth > 1024 && sidebar) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
