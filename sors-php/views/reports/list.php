<?php
/**
 * Reports List View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Reports';
?>

<div class="page-header">
    <h1><i class="fas fa-chart-bar"></i> Reports</h1>
    <div class="header-actions">
        <a href="index.php?route=reports/generate" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate Report
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

<?php if (empty($reports)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-file-alt"></i>
        </div>
        <h3>No Reports Generated Yet</h3>
        <p>Start by generating your first report to track system performance and analytics.</p>
        <a href="index.php?route=reports/generate" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate First Report
        </a>
    </div>
<?php else: ?>
    <div class="reports-grid">
        <?php foreach ($reports as $report): ?>
            <div class="report-card">
                <div class="report-header">
                    <h3><?php echo htmlspecialchars($report['title'] ?? 'Untitled Report'); ?></h3>
                    <div class="report-meta">
                        <span class="report-type"><?php echo htmlspecialchars($report['type'] ?? 'General'); ?></span>
                        <span class="report-date">
                            <i class="fas fa-calendar"></i>
                            <?php echo date('M j, Y', strtotime($report['created_at'] ?? 'now')); ?>
                        </span>
                    </div>
                </div>

                <div class="report-description">
                    <?php echo htmlspecialchars($report['description'] ?? 'No description available.'); ?>
                </div>

                <div class="report-actions">
                    <a href="index.php?route=reports/view&id=<?php echo $report['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="index.php?route=reports/download&id=<?php echo $report['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
