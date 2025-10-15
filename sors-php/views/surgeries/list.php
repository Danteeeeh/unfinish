<?php
/**
 * Surgeries List View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Surgeries';
?>

<div class="page-header">
    <h1><i class="fas fa-calendar-alt"></i> Surgeries</h1>
    <div class="header-actions">
        <a href="index.php?route=surgeries/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Schedule Surgery
        </a>
        <a href="index.php?route=surgeries/calendar" class="btn btn-secondary">
            <i class="fas fa-calendar"></i> Calendar View
        </a>
    </div>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (empty($surgeries)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-calendar-times"></i>
        </div>
        <h3>No Surgeries Scheduled</h3>
        <p>Start by scheduling your first surgery to manage operating room utilization.</p>
        <a href="index.php?route=surgeries/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Schedule First Surgery
        </a>
    </div>
<?php else: ?>
    <div class="surgeries-grid">
        <?php foreach ($surgeries as $surgery): ?>
            <div class="surgery-card">
                <div class="surgery-header">
                    <h3><?php echo htmlspecialchars($surgery['patient_name'] ?? 'Unknown Patient'); ?></h3>
                    <span class="surgery-status status-<?php echo strtolower($surgery['status'] ?? 'scheduled'); ?>">
                        <?php echo ucfirst($surgery['status'] ?? 'Scheduled'); ?>
                    </span>
                </div>

                <div class="surgery-details">
                    <div class="detail-row">
                        <div class="detail-item">
                            <i class="fas fa-stethoscope"></i>
                            <span><?php echo htmlspecialchars($surgery['surgery_type'] ?? 'General Surgery'); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <span><?php echo date('M j, Y', strtotime($surgery['scheduled_date'] ?? 'today')); ?></span>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo date('g:i A', strtotime($surgery['scheduled_time'] ?? '09:00')); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-hourglass-half"></i>
                            <span><?php echo $surgery['estimated_duration'] ?? 60; ?> min</span>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <i class="fas fa-hospital"></i>
                            <span>Room <?php echo htmlspecialchars($surgery['room_name'] ?? 'Unassigned'); ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-user-md"></i>
                            <span>Dr. <?php echo htmlspecialchars($surgery['surgeon_name'] ?? 'Unassigned'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="surgery-actions">
                    <a href="index.php?route=surgeries/view&id=<?php echo $surgery['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="index.php?route=surgeries/edit&id=<?php echo $surgery['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="index.php?route=surgeries/delete&id=<?php echo $surgery['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this surgery?')">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
