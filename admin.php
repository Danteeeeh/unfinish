<?php
require_once 'config.php';
requireAdmin();

$conn = getDBConnection();
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_user') {
        $user_id = trim($_POST['user_id'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';

        if (empty($user_id) || empty($full_name)) {
            $error = 'User ID and Full Name are required';
        } else {
            $stmt = $conn->prepare("INSERT INTO users (user_id, full_name, email, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $user_id, $full_name, $email, $role);

            if ($stmt->execute()) {
                $message = 'User added successfully';
            } else {
                $error = 'Error: User ID already exists or database error';
            }
            $stmt->close();
        }
    } elseif ($action === 'update_status') {
        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            $message = 'User status updated successfully';
        } else {
            $error = 'Error updating user status';
        }
        $stmt->close();
    } elseif ($action === 'delete_user') {
        $id = intval($_POST['id'] ?? 0);

        // Prevent deleting yourself
        if ($id == $_SESSION['db_id']) {
            $error = 'You cannot delete your own account';
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $message = 'User deleted successfully';
            } else {
                $error = 'Error deleting user';
            }
            $stmt->close();
        }
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$active_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'")->fetch_assoc()['count'];
$admin_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")->fetch_assoc()['count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            /* Unified DNMS Color System - Professional Blue */
            --dnms-primary: #2563eb;          /* Professional blue */
            --dnms-primary-dark: #1d4ed8;     /* Darker blue for hover */
            --dnms-primary-light: #3b82f6;    /* Lighter blue for accents */
            --dnms-secondary: #10b981;        /* Success green */
            --dnms-warning: #f59e0b;          /* Warning orange */
            --dnms-danger: #ef4444;           /* Error red */
            --dnms-info: #06b6d4;             /* Info cyan */

            /* Background Colors */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --bg-card: #ffffff;

            /* Text Colors */
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;

            /* Border and Shadow */
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Dark Theme - Consistent with light theme colors */
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --bg-card: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --border-color: #334155;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-secondary);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 2rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
        }

        .admin-header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }

        .theme-toggle {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: 50px;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .theme-toggle:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-md);
        }

        .theme-toggle i {
            font-size: 1.125rem;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .toggle-ball {
            width: 24px;
            height: 24px;
            background: var(--dnms-primary);
            border-radius: 50%;
            transition: transform 0.3s ease;
            position: relative;
        }

        [data-theme="dark"] .toggle-ball {
            transform: translateX(24px);
        }

        .admin-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: var(--dnms-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
            animation: fadeInUp 0.6s ease-out;
        }

        .admin-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        .dashboard-container {
            background: var(--bg-card);
            border-radius: 20px;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border-color);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .navbar {
            background: var(--dnms-primary);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar .user-info span {
            font-weight: 500;
        }

        .admin-content {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--dnms-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dnms-primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.125rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .users-section {
            margin-bottom: 3rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--dnms-primary);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            background: var(--dnms-primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        .btn-danger {
            background: var(--dnms-danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-primary);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .users-table th {
            background: var(--bg-secondary);
            font-weight: 600;
            color: var(--text-primary);
        }

        .users-table tr:hover {
            background: var(--bg-secondary);
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: var(--dnms-secondary);
            color: white;
        }

        .status-inactive {
            background: var(--dnms-warning);
            color: white;
        }

        .role-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-admin {
            background: var(--dnms-danger);
            color: white;
        }

        .role-user {
            background: var(--dnms-info);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }

        .form-section {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .form-section h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-group input,
        .form-group select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.875rem;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--dnms-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--dnms-secondary);
            color: var(--dnms-secondary);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--dnms-danger);
            color: var(--dnms-danger);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .admin-header {
                margin-bottom: 2rem;
            }

            .theme-toggle {
                position: relative;
                top: auto;
                right: auto;
                margin: 1rem auto;
            }

            .admin-title {
                font-size: 2.5rem;
            }

            .navbar {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .users-table {
                font-size: 0.875rem;
            }

            .users-table th,
            .users-table td {
                padding: 0.75rem 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .admin-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">⚙️ Admin Panel</h1>
            <p class="admin-subtitle">Manage users, system settings, and monitor application health</p>

            <!-- Theme Toggle - Admin Only -->
            <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
                <i class="fas fa-sun"></i>
                <div class="toggle-ball"></div>
                <i class="fas fa-moon"></i>
            </div>
        </div>

        <div class="dashboard-container">
            <div class="navbar">
                <h2>🛠️ User Management System</h2>
                <div class="user-info">
                    <span>👤 <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                    <a href="logout.php" class="btn btn-secondary btn-sm">🚪 Logout</a>
                </div>
            </div>

            <div class="admin-content">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value"><?php echo $total_users; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-value"><?php echo $active_users; ?></div>
                        <div class="stat-label">Active Users</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-value"><?php echo $admin_users; ?></div>
                        <div class="stat-label">Administrators</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-value"><?php echo round(($active_users / max($total_users, 1)) * 100); ?>%</div>
                        <div class="stat-label">Activity Rate</div>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- User Management Section -->
                <div class="users-section">
                    <div class="section-header">
                        <h3 class="section-title">👥 User Management</h3>
                        <button class="btn btn-primary" onclick="toggleAddUserForm()">
                            <i class="fas fa-plus"></i>
                            Add New User
                        </button>
                    </div>

                    <!-- Add User Form -->
                    <div id="addUserForm" class="form-section" style="display: none;">
                        <h3>➕ Add New User</h3>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_user">

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="user_id">User ID *</label>
                                    <input type="text" id="user_id" name="user_id" required>
                                </div>

                                <div class="form-group">
                                    <label for="full_name">Full Name *</label>
                                    <input type="text" id="full_name" name="full_name" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email">
                                </div>

                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select id="role" name="role">
                                        <option value="user">User</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" onclick="toggleAddUserForm()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add User</button>
                            </div>
                        </form>
                    </div>

                    <!-- Users Table -->
                    <div class="table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="role-badge <?php echo $user['role']; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $user['status']; ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                    <select name="status" onchange="this.form.submit()" class="btn btn-sm btn-secondary">
                                                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                        <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                    </select>
                                                </form>

                                                <?php if ($user['id'] != $_SESSION['db_id']): ?>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                        <input type="hidden" name="action" value="delete_user">
                                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Theme Management - Admin Only
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            showNotification(`Switched to ${newTheme} mode!`, 'success');
        }

        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Update toggle button
            const toggleBall = document.querySelector('.toggle-ball');
            if (savedTheme === 'dark') {
                toggleBall.style.transform = 'translateX(24px)';
            }
        }

        function toggleAddUserForm() {
            const form = document.getElementById('addUserForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const colors = {
                success: 'var(--dnms-secondary)',
                error: 'var(--dnms-danger)',
                warning: 'var(--dnms-warning)',
                info: 'var(--dnms-info)'
            };

            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colors[type]};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: var(--shadow-lg);
                z-index: 10001;
                max-width: 350px;
                font-family: 'Inter', sans-serif;
                animation: slideInRight 0.3s ease;
            `;

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-${getIcon(type)}" style="font-size: 18px;"></i>
                    <p style="margin: 0; font-size: 14px; line-height: 1.4;">${message}</p>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideInRight 0.3s ease reverse';
                    setTimeout(() => notification.parentNode.removeChild(notification), 300);
                }
            }, 3000);
        }

        function getIcon(type) {
            const icons = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            return icons[type] || 'info-circle';
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();

            // Add CSS animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        opacity: 0;
                        transform: translateX(100%);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>
