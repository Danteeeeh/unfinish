<?php $pageTitle = 'Generate Report'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-file-medical"></i> Generate Report</h1>
        <a href="index.php?route=reports/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>Select Test for Report Generation</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($verifiedTests)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Patient</th>
                            <th>Test Name</th>
                            <th>Verified Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($verifiedTests as $test): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($test['test_id']); ?></td>
                                <td><?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($test['test_name']); ?></td>
                                <td><?php echo date(DISPLAY_DATETIME_FORMAT, strtotime($test['verification_date'])); ?></td>
                                <td>
                                    <form method="POST" action="index.php?route=reports/generate" style="display:inline;">
                                        <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-file-pdf"></i> Generate PDF
                                        </button>
                                    </form>
                                    <a href="index.php?route=reports/view&test_id=<?php echo $test['id']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Preview
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-muted">No verified tests available for report generation.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
