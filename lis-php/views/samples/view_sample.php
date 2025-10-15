<?php $pageTitle = 'View Sample Details'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-vial"></i> Sample Details</h1>
        <div>
            <a href="index.php?route=samples/list" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="index.php?route=samples/edit&id=<?php echo $sample['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Sample Information</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-group">
                        <label>Sample ID:</label>
                        <p><?php echo htmlspecialchars($sample['sample_id']); ?></p>
                    </div>

                    <div class="detail-group">
                        <label>Patient Name:</label>
                        <p><?php echo htmlspecialchars($sample['first_name'] . ' ' . $sample['last_name']); ?></p>
                    </div>

                    <div class="detail-group">
                        <label>Patient ID:</label>
                        <p><?php echo htmlspecialchars($sample['patient_id']); ?></p>
                    </div>

                    <div class="detail-group">
                        <label>Sample Type:</label>
                        <p><?php echo htmlspecialchars(ucfirst($sample['sample_type'])); ?></p>
                    </div>

                    <div class="detail-group">
                        <label>Collection Date:</label>
                        <p><?php echo date('M d, Y H:i', strtotime($sample['collection_date'])); ?></p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-group">
                        <label>Status:</label>
                        <p>
                            <span class="badge badge-<?php 
                                echo $sample['status'] == 'pending' ? 'warning' : 
                                     ($sample['status'] == 'processing' ? 'info' : 
                                     ($sample['status'] == 'completed' ? 'success' : 'secondary')); 
                            ?>">
                                <?php echo htmlspecialchars(ucfirst($sample['status'])); ?>
                            </span>
                        </p>
                    </div>

                    <?php if (!empty($sample['priority'])): ?>
                    <div class="detail-group">
                        <label>Priority:</label>
                        <p>
                            <span class="badge badge-<?php 
                                echo $sample['priority'] == 'urgent' ? 'danger' : 
                                     ($sample['priority'] == 'high' ? 'warning' : 'secondary'); 
                            ?>">
                                <?php echo htmlspecialchars(ucfirst($sample['priority'])); ?>
                            </span>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($sample['received_date'])): ?>
                    <div class="detail-group">
                        <label>Received Date:</label>
                        <p><?php echo date('M d, Y H:i', strtotime($sample['received_date'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($sample['tested_date'])): ?>
                    <div class="detail-group">
                        <label>Tested Date:</label>
                        <p><?php echo date('M d, Y H:i', strtotime($sample['tested_date'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($sample['completed_date'])): ?>
                    <div class="detail-group">
                        <label>Completed Date:</label>
                        <p><?php echo date('M d, Y H:i', strtotime($sample['completed_date'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($sample['notes'])): ?>
            <div class="detail-group">
                <label>Notes:</label>
                <p><?php echo nl2br(htmlspecialchars($sample['notes'])); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($sample['storage_location'])): ?>
            <div class="detail-group">
                <label>Storage Location:</label>
                <p><?php echo htmlspecialchars($sample['storage_location']); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($sample['status'] == 'completed'): ?>
    <div class="card mt-3">
        <div class="card-header">
            <h3>Test Results</h3>
        </div>
        <div class="card-body">
            <p>View associated test results for this sample.</p>
            <a href="index.php?route=tests/list&sample_id=<?php echo $sample['id']; ?>" class="btn btn-primary">
                <i class="fas fa-flask"></i> View Test Results
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.detail-group {
    margin-bottom: 1rem;
}

.detail-group label {
    font-weight: bold;
    color: #555;
    display: block;
    margin-bottom: 0.25rem;
}

.detail-group p {
    margin: 0;
    color: #333;
}

.badge {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
