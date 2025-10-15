<?php $pageTitle = 'DNMS Dashboard'; ?>

<div class="dashboard-page">
    <div class="dashboard-header">
        <h1 class="page-title">🍎 Nutrition Management System</h1>
        <p class="page-subtitle">Track your nutrition, plan your meals, and achieve your health goals</p>
    </div>

    <?php if (isset($stats)): ?>
        <!-- Enhanced Statistics Cards -->
        <div class="stats-container">
            <div class="stats-grid">
                <div class="stat-card calories">
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($stats['daily_calories'] ?? 0); ?></div>
                        <div class="stat-label">Daily Calories</div>
                        <div class="stat-subtitle">Avg. intake today</div>
                    </div>
                    <div class="stat-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min(($stats['daily_calories'] ?? 0) / 2000 * 100, 100); ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="stat-card protein">
                    <div class="stat-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($stats['daily_protein'] ?? 0); ?>g</div>
                        <div class="stat-label">Protein Intake</div>
                        <div class="stat-subtitle">Daily target: 150g</div>
                    </div>
                    <div class="stat-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min(($stats['daily_protein'] ?? 0) / 150 * 100, 100); ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="stat-card goals">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($stats['goals_achieved'] ?? 0); ?></div>
                        <div class="stat-label">Goals Achieved</div>
                        <div class="stat-subtitle"><?php echo number_format($stats['active_goals'] ?? 0); ?> active goals</div>
                    </div>
                    <div class="stat-progress">
                        <div class="progress-ring">
                            <svg width="60" height="60" viewBox="0 0 60 60">
                                <circle cx="30" cy="30" r="25" stroke="rgba(255,255,255,0.2)" stroke-width="4" fill="none"/>
                                <circle cx="30" cy="30" r="25" stroke="#10b981"
                                        stroke-width="4" fill="none"
                                        stroke-dasharray="<?php echo (2 * pi() * 25); ?>"
                                        stroke-dashoffset="<?php echo (2 * pi() * 25) * (1 - min(($stats['goals_achieved'] ?? 0) / 10, 1)); ?>"
                                        transform="rotate(-90 30 30)"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card meals">
                    <div class="stat-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($stats['total_meals'] ?? 0); ?></div>
                        <div class="stat-label">Total Meals</div>
                        <div class="stat-subtitle">This week</div>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up text-green"></i>
                        <span class="trend-text">+12%</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Quick Actions Section -->
    <div class="quick-actions-section">
        <h2 class="section-title">🚀 Quick Actions</h2>
        <div class="actions-grid">
            <a href="../food/search.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="action-content">
                    <h3>Search Foods</h3>
                    <p>Find nutritional information for any food</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../meal/add.php?food_id=1" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="action-content">
                    <h3>Add Meal</h3>
                    <p>Log your food intake with detailed nutrition</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../meal/planner.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="action-content">
                    <h3>Meal Planner</h3>
                    <p>Get AI-powered meal recommendations</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../meal/history.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="action-content">
                    <h3>Meal History</h3>
                    <p>View your nutrition tracking history</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        </div>
    </div>

    <?php if (empty($stats) || ($stats['total_foods'] ?? 0) == 0): ?>
        <div class="setup-card">
            <div class="setup-icon">
                <i class="fas fa-database"></i>
            </div>
            <div class="setup-content">
                <h3>Database Setup Required</h3>
                <p>Looks like the DNMS database tables aren't set up yet. Let's get you started with comprehensive nutrition tracking!</p>
                <a href="../setup_database.php" class="btn btn-primary">
                    <i class="fas fa-cog"></i>
                    Set Up Database
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recent Activity Section -->
    <div class="activity-section">
        <div class="section-header">
            <h2 class="section-title">📈 Recent Nutrition Activity</h2>
            <a href="../meal/history.php" class="view-all-link">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="activity-container">
            <?php if (isset($recentMeals) && !empty($recentMeals)): ?>
                <?php foreach ($recentMeals as $meal): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title"><?php echo htmlspecialchars($meal['meal_name'] ?? 'Meal'); ?></div>
                            <div class="activity-meta">
                                <span class="meal-type"><?php echo ucfirst($meal['meal_type'] ?? 'lunch'); ?></span>
                                <span class="meal-calories"><?php echo number_format($meal['total_calories'] ?? 0); ?> cal</span>
                                <span class="meal-date"><?php echo htmlspecialchars($meal['meal_date'] ?? ''); ?></span>
                            </div>
                        </div>
                        <div class="activity-actions">
                            <a href="../meal/history.php?action=view&id=<?php echo $meal['id'] ?? ''; ?>" class="btn btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-activity">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3>No Recent Activity</h3>
                    <p>Start your nutrition journey by logging your first meal!</p>
                    <a href="../food/search.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Log Your First Meal
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.dashboard-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.dashboard-header {
    text-align: center;
    margin-bottom: 4rem;
}

.page-title {
    font-size: 3.5rem;
    font-weight: 800;
    background: var(--nutrition-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
    animation: fadeInUp 0.6s ease-out;
}

.page-subtitle {
    font-size: 1.25rem;
    color: var(--text-secondary);
    max-width: 600px;
    margin: 0 auto;
    animation: fadeInUp 0.6s ease-out 0.1s both;
}

/* Enhanced Stats Container */
.stats-container {
    margin-bottom: 4rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.stat-card {
    background: var(--bg-primary);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    animation: slideInUp 0.6s ease-out;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--nutrition-gradient);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--nutrition-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    color: white;
    box-shadow: var(--shadow-md);
}

.stat-content {
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    line-height: 1;
}

.stat-label {
    font-size: 1.125rem;
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.stat-subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.stat-progress {
    margin-top: 1rem;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: var(--bg-tertiary);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--nutrition-gradient);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.stat-trend {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.trend-text {
    font-weight: 600;
}

.text-green {
    color: var(--success-color);
}

/* Quick Actions Section */
.quick-actions-section {
    margin-bottom: 4rem;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.action-card {
    background: var(--bg-primary);
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid var(--border-color);
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--nutrition-gradient);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.action-card:hover::before {
    transform: scaleX(1);
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.action-icon {
    width: 50px;
    height: 50px;
    background: var(--nutrition-gradient);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.action-content {
    flex: 1;
}

.action-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.action-content p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.4;
}

.action-arrow {
    color: var(--text-secondary);
    font-size: 1.125rem;
    transition: transform 0.3s ease, color 0.3s ease;
}

.action-card:hover .action-arrow {
    transform: translateX(4px);
    color: var(--primary-color);
}

/* Setup Card */
.setup-card {
    background: linear-gradient(135deg, var(--bg-primary) 0%, rgba(78, 205, 196, 0.05) 100%);
    border-radius: 20px;
    padding: 3rem;
    text-align: center;
    border: 2px dashed var(--border-color);
    margin-bottom: 3rem;
    animation: pulse 2s infinite;
}

.setup-icon {
    width: 80px;
    height: 80px;
    background: var(--nutrition-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin: 0 auto 1.5rem;
}

.setup-content h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.setup-content p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

/* Activity Section */
.activity-section {
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
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.view-all-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.view-all-link:hover {
    color: var(--secondary-color);
    gap: 0.75rem;
}

.activity-container {
    background: var(--bg-primary);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.3s ease;
    gap: 1rem;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item:hover {
    background: var(--bg-secondary);
    border-radius: 12px;
    margin: 0 -1rem;
    padding: 1.5rem 2rem;
}

.activity-icon {
    width: 50px;
    height: 50px;
    background: var(--bg-tertiary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    font-size: 1.125rem;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.activity-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.meal-type {
    background: var(--bg-tertiary);
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-weight: 500;
}

.meal-calories {
    color: var(--success-color);
    font-weight: 600;
}

.activity-actions {
    display: flex;
    gap: 0.5rem;
}

.empty-activity {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: var(--bg-tertiary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    font-size: 2rem;
    margin: 0 auto 1.5rem;
}

.empty-activity h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.empty-activity p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
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

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.02);
        opacity: 0.8;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-page {
        padding: 1rem;
    }

    .page-title {
        font-size: 2.5rem;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .actions-grid {
        grid-template-columns: 1fr;
    }

    .section-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .activity-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .activity-meta {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stat-value {
        font-size: 2rem;
    }

    .page-title {
        font-size: 2rem;
    }
}
</style>

<script>
// Enhanced dashboard interactions
document.addEventListener('DOMContentLoaded', function() {
    // Animate stat cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.stat-card, .action-card, .activity-item').forEach(el => {
        observer.observe(el);
    });

    // Add hover effects to action cards
    document.querySelectorAll('.action-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>
