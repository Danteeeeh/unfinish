<?php
/**
 * Staff Schedule View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Staff Schedule';
?>

<div class="page-header">
    <h1><i class="fas fa-calendar"></i> Staff Schedule</h1>
    <div class="header-actions">
        <a href="index.php?route=staff/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Staff
        </a>
        <a href="index.php?route=staff/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Staff
        </a>
    </div>
</div>

<div class="schedule-container">
    <div class="schedule-summary">
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-number"><?php echo $schedule['surgeons'] ?? 0; ?></div>
                    <div class="summary-label">Surgeons</div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-user-nurse"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-number"><?php echo $schedule['nurses'] ?? 0; ?></div>
                    <div class="summary-label">Nurses</div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-number"><?php echo $schedule['technicians'] ?? 0; ?></div>
                    <div class="summary-label">Technicians</div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-number"><?php echo $schedule['assistants'] ?? 0; ?></div>
                    <div class="summary-label">Assistants</div>
                </div>
            </div>
        </div>
    </div>

    <div class="schedule-details">
        <div class="schedule-section">
            <h3><i class="fas fa-user-md"></i> Surgeons Schedule</h3>
            <div class="schedule-list">
                <?php for ($i = 1; $i <= ($schedule['surgeons'] ?? 0); $i++): ?>
                    <div class="schedule-item">
                        <div class="staff-avatar">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="schedule-info">
                            <div class="staff-name">Dr. Surgeon <?php echo $i; ?></div>
                            <div class="schedule-time">
                                <i class="fas fa-clock"></i>
                                8:00 AM - 5:00 PM
                            </div>
                        </div>
                        <div class="schedule-status available">
                            <i class="fas fa-check-circle"></i>
                            Available
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="schedule-section">
            <h3><i class="fas fa-user-nurse"></i> Nurses Schedule</h3>
            <div class="schedule-list">
                <?php for ($i = 1; $i <= ($schedule['nurses'] ?? 0); $i++): ?>
                    <div class="schedule-item">
                        <div class="staff-avatar">
                            <i class="fas fa-user-nurse"></i>
                        </div>
                        <div class="schedule-info">
                            <div class="staff-name">Nurse <?php echo $i; ?></div>
                            <div class="schedule-time">
                                <i class="fas fa-clock"></i>
                                7:00 AM - 7:00 PM
                            </div>
                        </div>
                        <div class="schedule-status available">
                            <i class="fas fa-check-circle"></i>
                            Available
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="schedule-section">
            <h3><i class="fas fa-tools"></i> Technicians Schedule</h3>
            <div class="schedule-list">
                <?php for ($i = 1; $i <= ($schedule['technicians'] ?? 0); $i++): ?>
                    <div class="schedule-item">
                        <div class="staff-avatar">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="schedule-info">
                            <div class="staff-name">Tech <?php echo $i; ?></div>
                            <div class="schedule-time">
                                <i class="fas fa-clock"></i>
                                8:00 AM - 6:00 PM
                            </div>
                        </div>
                        <div class="schedule-status available">
                            <i class="fas fa-check-circle"></i>
                            Available
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="schedule-section">
            <h3><i class="fas fa-hands-helping"></i> Assistants Schedule</h3>
            <div class="schedule-list">
                <?php for ($i = 1; $i <= ($schedule['assistants'] ?? 0); $i++): ?>
                    <div class="schedule-item">
                        <div class="staff-avatar">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <div class="schedule-info">
                            <div class="staff-name">Assistant <?php echo $i; ?></div>
                            <div class="schedule-time">
                                <i class="fas fa-clock"></i>
                                9:00 AM - 5:00 PM
                            </div>
                        </div>
                        <div class="schedule-status available">
                            <i class="fas fa-check-circle"></i>
                            Available
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Staff Schedule Styles */
.schedule-container {
    max-width: 1200px;
    margin: 0 auto;
}

.schedule-summary {
    margin-bottom: 2rem;
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.summary-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.summary-content {
    flex: 1;
}

.summary-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
}

.summary-label {
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.schedule-details {
    display: grid;
    gap: 2rem;
}

.schedule-section {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
}

.schedule-section h3 {
    color: var(--text-primary);
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
}

.schedule-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.schedule-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-secondary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.schedule-item:hover {
    background: var(--bg-tertiary);
    transform: translateX(4px);
}

.staff-avatar {
    width: 40px;
    height: 40px;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
    flex-shrink: 0;
}

.schedule-info {
    flex: 1;
}

.staff-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.schedule-time {
    color: var(--text-secondary);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.schedule-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    flex-shrink: 0;
}

.schedule-status.available {
    background: #d1fae5;
    color: #065f46;
}

.schedule-status.busy {
    background: #fee2e2;
    color: #991b1b;
}

.schedule-status.off-duty {
    background: #f3f4f6;
    color: #6b7280;
}

@media (max-width: 768px) {
    .schedule-container {
        padding: 0 1rem;
    }

    .summary-cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .schedule-section {
        padding: 1rem;
    }

    .schedule-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .schedule-status {
        align-self: flex-end;
    }
}

@media (max-width: 480px) {
    .summary-cards {
        grid-template-columns: 1fr;
    }
}
</style>
