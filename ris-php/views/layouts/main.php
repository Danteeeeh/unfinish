<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>RIS - Radiology Information System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dashboard specific styles */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 1.2em;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .stat-card p {
            font-size: 2.5em;
            font-weight: bold;
            margin: 0;
        }

        .recent-studies {
            margin-top: 40px;
        }

        .recent-studies-header {
            margin-bottom: 20px;
        }

        .recent-studies-header h3 {
            color: #2c3e50;
            font-size: 1.8em;
        }

        .study-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #3498db;
        }

        .study-item .patient-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .study-item .study-type {
            color: #7f8c8d;
            margin-top: 5px;
        }

        .study-item .study-date {
            color: #95a5a6;
            font-size: 0.9em;
        }

        .no-studies {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .action-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            text-decoration: none;
            color: #2c3e50;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .action-card h4 {
            font-size: 1.3em;
            margin-bottom: 10px;
            color: #3498db;
        }

        .navbar {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-brand {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .nav-menu {
            display: flex;
            gap: 1rem;
            align-items: center;
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
            cursor: pointer;
        }

        .btn-primary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            border: 1px solid #dc2626;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .content {
            padding: 2rem;
            min-height: calc(100vh - 80px);
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .btn {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }

            .content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <nav class="navbar">
            <div class="nav-brand">🏥 RIS Dashboard</div>
            <div class="nav-menu">
                <a href="../dashboard.php" class="btn btn-primary">🏠 Main Dashboard</a>
                <a href="index.php?route=patients/list" class="btn btn-secondary">Patients</a>
                <a href="index.php?route=studies/list" class="btn btn-secondary">Studies</a>
                <a href="index.php?route=reports/list" class="btn btn-secondary">Reports</a>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </nav>

        <div class="content">
            <?php if (isset($title) && $title === 'Dashboard'): ?>
                <!-- Dashboard Content -->
                <div class="page-header">
                    <h1>Radiology Information System</h1>
                    <p>Manage patients, studies, and radiology operations</p>
                </div>

                <?php if (isset($stats)): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Patients</h3>
                        <p><?php echo $stats['total_patients']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Studies Today</h3>
                        <p><?php echo $stats['studies_today']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Available Modalities</h3>
                        <p><?php echo $stats['available_modalities']; ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (isset($recentStudies) && !empty($recentStudies)): ?>
                <div class="recent-studies">
                    <div class="recent-studies-header">
                        <h3>Recent Studies</h3>
                    </div>
                    <?php foreach ($recentStudies as $study): ?>
                    <div class="study-item">
                        <div class="patient-name"><?php echo htmlspecialchars($study['patient_name'] ?? 'Unknown Patient'); ?></div>
                        <div class="study-type"><?php echo htmlspecialchars($study['study_type'] ?? 'General Study'); ?></div>
                        <div class="study-date"><?php echo htmlspecialchars($study['study_date'] ?? 'No Date'); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ((!isset($stats) || $stats['total_patients'] == 0) && (!isset($recentStudies) || empty($recentStudies))): ?>
                <div class="no-studies">
                    <h3>Welcome to RIS!</h3>
                    <p>Your radiology information system is ready.</p>
                    <p>Add patients and schedule studies to get started.</p>
                </div>
                <?php endif; ?>

                <div class="quick-actions">
                    <a href="index.php?route=patients/list" class="action-card">
                        <h4>👥 Manage Patients</h4>
                        <p>Add and manage patient information</p>
                    </a>
                    <a href="index.php?route=studies/list" class="action-card">
                        <h4>🩺 Schedule Studies</h4>
                        <p>Create and manage radiology studies</p>
                    </a>
                    <a href="index.php?route=reports/list" class="action-card">
                        <h4>📊 View Reports</h4>
                        <p>Access study reports and results</p>
                    </a>
                </div>

            <?php else: ?>
                <!-- Other page content -->
                <div class="page-header">
                    <h1><?php echo isset($title) ? $title : 'RIS System'; ?></h1>
                </div>
                <?php if (isset($content)) echo $content; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
