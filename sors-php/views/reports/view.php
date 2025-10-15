<?php
/**
 * View Report View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Report Details';
?>

<div class="page-header">
    <h1><i class="fas fa-eye"></i> Report Details</h1>
    <div class="header-actions">
        <a href="index.php?route=reports/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <a href="index.php?route=reports/download&id=<?php echo $report['id']; ?>" class="btn btn-primary">
            <i class="fas fa-download"></i> Download
        </a>
    </div>
</div>

<div class="report-details">
    <div class="report-header">
        <h2><?php echo htmlspecialchars($report['title'] ?? 'Untitled Report'); ?></h2>
        <div class="report-meta">
            <div class="meta-item">
                <strong>Type:</strong> <?php echo htmlspecialchars($report['type'] ?? 'General'); ?>
            </div>
            <div class="meta-item">
                <strong>Generated:</strong> <?php echo date('M j, Y \a\t g:i A', strtotime($report['created_at'] ?? 'now')); ?>
            </div>
            <div class="meta-item">
                <strong>Format:</strong> <?php echo strtoupper($report['format'] ?? 'HTML'); ?>
            </div>
        </div>
    </div>

    <div class="report-content">
        <?php if (isset($reportData) && !empty($reportData)): ?>
            <?php if (isset($reportData['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($reportData['error']); ?>
                </div>
            <?php else: ?>
                <div class="report-summary">
                    <h3><?php echo htmlspecialchars($reportData['title'] ?? 'Report Summary'); ?></h3>
                    <p><?php echo htmlspecialchars($reportData['data'] ?? 'No data available for this report.'); ?></p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Report data will be displayed here once generated.
            </div>
        <?php endif; ?>
    </div>

    <div class="report-actions">
        <a href="index.php?route=reports/generate" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate New Report
        </a>
        <a href="index.php?route=reports/list" class="btn btn-secondary">
            <i class="fas fa-list"></i> View All Reports
        </a>
    </div>
</div>
