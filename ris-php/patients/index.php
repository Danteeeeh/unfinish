<?php
/**
 * Patients Management Portal
 * Main landing page for patient management with enhanced functionality
 */

// Load configuration and authentication
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/db_functions.php';
require_once __DIR__ . '/../includes/utils.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get patient statistics
$totalPatients = getPatientCount();
$recentPatients = getRecentPatients(5);
$todayRegistrations = getTodayPatientRegistrations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients Management - RIS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --secondary-gradient: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);

            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-secondary);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--bg-primary);
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card.primary {
            background: var(--primary-gradient);
            color: white;
        }

        .stat-card.secondary {
            background: var(--secondary-gradient);
            color: white;
        }

        .stat-card.success {
            background: var(--success-gradient);
            color: white;
        }

        .stat-card.warning {
            background: var(--warning-gradient);
            color: white;
        }

        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .action-card {
            background: var(--bg-primary);
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-primary);
        }

        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .action-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--primary-gradient);
        }

        .action-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .action-description {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .recent-patients {
            background: var(--bg-primary);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .patient-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.3s ease;
        }

        .patient-item:hover {
            background: var(--bg-tertiary);
        }

        .patient-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .patient-meta {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .patient-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        .search-section {
            background: var(--bg-primary);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
        }

        .search-form {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-gradient);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .container {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .action-grid {
                grid-template-columns: 1fr;
            }

            .search-form {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-user-injured"></i> Patient Management Portal</h1>
        <p>Comprehensive patient management system for Radiology Information System</p>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $totalPatients; ?></div>
                <div class="stat-label">Total Patients</div>
            </div>
            <div class="stat-card secondary">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-number"><?php echo $todayRegistrations; ?></div>
                <div class="stat-label">Today Registrations</div>
            </div>
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-number"><?php echo count($recentPatients); ?></div>
                <div class="stat-label">Recent Activity</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo date('d'); ?></div>
                <div class="stat-label">Days This Month</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="action-grid">
            <a href="../index.php?route=patients/add" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h3 class="action-title">Add New Patient</h3>
                <p class="action-description">Register a new patient in the system</p>
                <span class="btn btn-primary">Add Patient</span>
            </a>

            <a href="../index.php?route=patients/list" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-list-ul"></i>
                </div>
                <h3 class="action-title">View All Patients</h3>
                <p class="action-description">Browse and manage all registered patients</p>
                <span class="btn btn-primary">View List</span>
            </a>

            <a href="../index.php?route=patients/search" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="action-title">Search Patients</h3>
                <p class="action-description">Find patients by name, ID, or other criteria</p>
                <span class="btn btn-primary">Search</span>
            </a>

            <a href="../index.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <h3 class="action-title">Back to Main</h3>
                <p class="action-description">Return to the main RIS dashboard</p>
                <span class="btn btn-primary">Go Back</span>
            </a>
        </div>

        <!-- Quick Search -->
        <div class="search-section">
            <h2 class="section-title">Quick Patient Search</h2>
            <form class="search-form" action="../index.php?route=patients/search" method="GET">
                <input type="hidden" name="route" value="patients/search">
                <input type="text" class="search-input" name="query" placeholder="Search by patient name, ID, or phone number..." required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </form>
        </div>

        <!-- Recent Patients -->
        <div class="recent-patients">
            <h2 class="section-title">Recent Patient Registrations</h2>
            <?php if (empty($recentPatients)): ?>
                <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                    No recent patient registrations
                </p>
            <?php else: ?>
                <?php foreach ($recentPatients as $patient): ?>
                    <div class="patient-item">
                        <div class="patient-info">
                            <h4><?php echo htmlspecialchars($patient['name'] ?? 'Unknown Patient'); ?></h4>
                            <div class="patient-meta">
                                <span><i class="fas fa-id-card"></i> ID: <?php echo htmlspecialchars($patient['id'] ?? 'N/A'); ?></span>
                                <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></span>
                                <span><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($patient['registration_date'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                        <div class="patient-actions">
                            <a href="../index.php?route=patients/view&id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="../index.php?route=patients/edit&id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="../index.php?route=patients/list" class="btn btn-primary">View All Patients</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Add some interactive functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat cards on load
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Add smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
