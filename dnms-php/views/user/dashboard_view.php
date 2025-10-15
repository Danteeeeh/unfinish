<?php $pageTitle = 'User Dashboard'; ?>

<!-- User Dashboard Content -->
<div class="user-dashboard-container">
    <div class="dashboard-welcome">
        <div class="welcome-header">
            <h1>Welcome to NutriTrack Pro</h1>
            <p>Your comprehensive nutrition management system</p>
        </div>

        <div class="welcome-stats">
            <div class="welcome-stat">
                <div class="stat-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>User Dashboard</h3>
                    <p>Manage your nutrition journey</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="quick-access">
        <h3>Quick Access</h3>

        <div class="access-grid">
            <a href="../foods/" class="access-card">
                <div class="access-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="access-info">
                    <h4>Food Search</h4>
                    <p>Search and browse our food database</p>
                </div>
                <div class="access-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../meals/" class="access-card">
                <div class="access-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="access-info">
                    <h4>Meal Logging</h4>
                    <p>Log your meals and track intake</p>
                </div>
                <div class="access-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../recipes/" class="access-card">
                <div class="access-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="access-info">
                    <h4>Recipes</h4>
                    <p>Create and manage recipes</p>
                </div>
                <div class="access-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../shopping/" class="access-card">
                <div class="access-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="access-info">
                    <h4>Shopping Lists</h4>
                    <p>Manage your grocery lists</p>
                </div>
                <div class="access-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../exercise/" class="access-card">
                <div class="access-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div class="access-info">
                    <h4>Exercise</h4>
                    <p>Track your workouts</p>
                </div>
                <div class="access-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../water/" class="access-card">
                <div class="access-icon">
                    <i class="fas fa-tint"></i>
                </div>
                <div class="access-info">
                    <h4>Water Tracking</h4>
                    <p>Monitor your hydration</p>
                </div>
                <div class="access-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../measurements/" class="access-card">
                <div class="access-icon">
                    <i class="fas fa-weight"></i>
                </div>
                <div class="access-info">
                    <h4>Measurements</h4>
                    <p>Track body measurements</p>
                </div>
                <div class="access-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../goals/" class="access-card">
                <div class="access-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="access-info">
                    <h4>Set Goals</h4>
                    <p>Set nutrition targets</p>
                </div>
                <div class="access-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../reports/" class="access-card">
                <div class="access-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="access-info">
                    <h4>Reports</h4>
                    <p>View nutrition analytics</p>
                </div>
                <div class="access-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- System Information -->
    <div class="system-info">
        <div class="info-card">
            <div class="info-header">
                <h3>System Information</h3>
            </div>

            <div class="info-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="info-item">
                        <span class="info-label">User ID:</span>
                        <span class="info-value"><?php echo $_SESSION['user_id']; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($stats)): ?>
                    <div class="info-item">
                        <span class="info-label">Total Foods:</span>
                        <span class="info-value"><?php echo number_format($stats['total_foods'] ?? 0); ?></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Meals Today:</span>
                        <span class="info-value"><?php echo number_format($stats['total_meals'] ?? 0); ?></span>
                    </div>
                <?php endif; ?>

                <div class="info-item">
                    <span class="info-label">System Status:</span>
                    <span class="info-value">
                        <span style="color: #28a745;">●</span> Online
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* User Dashboard Styling with LIS Design */
    .user-dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .dashboard-welcome {
        text-align: center;
        margin-bottom: 50px;
        padding: 40px 20px;
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
    }

    .welcome-header h1 {
        font-size: 36px;
        font-weight: 800;
        background: var(--nutrition-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 10px;
    }

    .welcome-header p {
        color: var(--text-secondary);
        font-size: 18px;
    }

    .welcome-stats {
        margin-top: 30px;
    }

    .welcome-stat {
        display: inline-flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: var(--bg-secondary);
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: var(--nutrition-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .stat-info h3 {
        margin: 0 0 5px 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .stat-info p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 14px;
    }

    .quick-access {
        margin-bottom: 40px;
    }

    .quick-access h3 {
        font-size: 24px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .access-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .access-card {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 25px;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.3s ease;
    }

    .access-card:hover {
        background: var(--bg-secondary);
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(78, 205, 196, 0.3);
    }

    .access-icon {
        width: 50px;
        height: 50px;
        background: var(--nutrition-gradient);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    .access-info {
        flex: 1;
    }

    .access-info h4 {
        margin: 0 0 5px 0;
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .access-info p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 14px;
        line-height: 1.4;
    }

    .access-arrow {
        color: var(--text-secondary);
        font-size: 16px;
        transition: transform 0.3s ease;
    }

    .access-card:hover .access-arrow {
        transform: translateX(5px);
        color: var(--nutrition-gradient);
    }

    .system-info {
        margin-bottom: 40px;
    }

    .info-card {
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .info-header {
        background: var(--nutrition-gradient);
        color: white;
        padding: 20px 25px;
    }

    .info-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .info-content {
        padding: 25px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-primary);
    }

    .info-value {
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .access-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .welcome-stat {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .welcome-header h1 {
            font-size: 28px;
        }

        .welcome-header p {
            font-size: 16px;
        }
    }
</style>
