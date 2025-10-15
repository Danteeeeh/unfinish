<?php $pageTitle = 'Sample Status'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-tasks"></i> Sample Status Overview</h1>
        <a href="index.php?route=samples/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Sample
        </a>
    </div>
    
    <div class="status-grid">
        <div class="status-card pending">
            <div class="status-header">
                <h3><i class="fas fa-clock"></i> Pending</h3>
                <span class="badge"><?php echo count($pending); ?></span>
            </div>
            <div class="status-body">
                <?php if (!empty($pending)): ?>
                    <?php foreach ($pending as $sample): ?>
                        <div class="sample-item">
                            <strong><?php echo htmlspecialchars($sample['sample_id']); ?></strong>
                            <p><?php echo htmlspecialchars($sample['first_name'] . ' ' . $sample['last_name']); ?></p>
                            <small><?php echo ucfirst($sample['sample_type']); ?></small>
                            <a href="index.php?route=samples/status&id=<?php echo $sample['id']; ?>" class="btn btn-sm">View</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No pending samples</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="status-card processing">
            <div class="status-header">
                <h3><i class="fas fa-spinner"></i> Processing</h3>
                <span class="badge"><?php echo count($processing); ?></span>
            </div>
            <div class="status-body">
                <?php if (!empty($processing)): ?>
                    <?php foreach ($processing as $sample): ?>
                        <div class="sample-item">
                            <strong><?php echo htmlspecialchars($sample['sample_id']); ?></strong>
                            <p><?php echo htmlspecialchars($sample['first_name'] . ' ' . $sample['last_name']); ?></p>
                            <small><?php echo ucfirst($sample['sample_type']); ?></small>
                            <a href="index.php?route=samples/status&id=<?php echo $sample['id']; ?>" class="btn btn-sm">View</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No samples in processing</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="status-card completed">
            <div class="status-header">
                <h3><i class="fas fa-check-circle"></i> Completed</h3>
                <span class="badge"><?php echo count($completed); ?></span>
            </div>
            <div class="status-body">
                <?php if (!empty($completed)): ?>
                    <?php foreach (array_slice($completed, 0, 5) as $sample): ?>
                        <div class="sample-item">
                            <strong><?php echo htmlspecialchars($sample['sample_id']); ?></strong>
                            <p><?php echo htmlspecialchars($sample['first_name'] . ' ' . $sample['last_name']); ?></p>
                            <small><?php echo ucfirst($sample['sample_type']); ?></small>
                            <a href="index.php?route=samples/status&id=<?php echo $sample['id']; ?>" class="btn btn-sm">View</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No completed samples</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="status-card rejected">
            <div class="status-header">
                <h3><i class="fas fa-times-circle"></i> Rejected</h3>
                <span class="badge"><?php echo count($rejected); ?></span>
            </div>
            <div class="status-body">
                <?php if (!empty($rejected)): ?>
                    <?php foreach ($rejected as $sample): ?>
                        <div class="sample-item">
                            <strong><?php echo htmlspecialchars($sample['sample_id']); ?></strong>
                            <p><?php echo htmlspecialchars($sample['first_name'] . ' ' . $sample['last_name']); ?></p>
                            <small><?php echo ucfirst($sample['sample_type']); ?></small>
                            <a href="index.php?route=samples/status&id=<?php echo $sample['id']; ?>" class="btn btn-sm">View</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No rejected samples</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
