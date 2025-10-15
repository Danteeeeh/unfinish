<?php $pageTitle = 'View Report'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-file-alt"></i> Laboratory Report</h1>
        <div>
            <a href="index.php?route=reports/download&test_id=<?php echo $test['id']; ?>" class="btn btn-primary">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <a href="index.php?route=reports/list" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>
    
    <div class="card report-card">
        <div class="report-header">
            <div class="lab-info">
                <h2><?php echo APP_NAME; ?></h2>
                <p>Laboratory Test Report</p>
            </div>
            <div class="report-meta">
                <p><strong>Report Date:</strong> <?php echo date(DISPLAY_DATE_FORMAT); ?></p>
                <p><strong>Test ID:</strong> <?php echo htmlspecialchars($test['test_id']); ?></p>
            </div>
        </div>
        
        <div class="report-body">
            <section class="report-section">
                <h3>Patient Information</h3>
                <div class="info-grid">
                    <div>
                        <strong>Patient ID:</strong>
                        <span><?php echo htmlspecialchars($test['patient_id']); ?></span>
                    </div>
                    <div>
                        <strong>Name:</strong>
                        <span><?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?></span>
                    </div>
                    <div>
                        <strong>Date of Birth:</strong>
                        <span><?php echo date(DISPLAY_DATE_FORMAT, strtotime($test['date_of_birth'])); ?></span>
                    </div>
                    <div>
                        <strong>Age:</strong>
                        <span><?php echo date_diff(date_create($test['date_of_birth']), date_create('today'))->y; ?> years</span>
                    </div>
                </div>
            </section>
            
            <section class="report-section">
                <h3>Test Information</h3>
                <div class="info-grid">
                    <div>
                        <strong>Test Name:</strong>
                        <span><?php echo htmlspecialchars($test['test_name']); ?></span>
                    </div>
                    <div>
                        <strong>Test Type:</strong>
                        <span><?php echo ucfirst($test['test_type']); ?></span>
                    </div>
                    <div>
                        <strong>Sample ID:</strong>
                        <span><?php echo htmlspecialchars($test['sample_id']); ?></span>
                    </div>
                    <div>
                        <strong>Sample Type:</strong>
                        <span><?php echo ucfirst($test['sample_type']); ?></span>
                    </div>
                    <div>
                        <strong>Order Date:</strong>
                        <span><?php echo date(DISPLAY_DATE_FORMAT, strtotime($test['order_date'])); ?></span>
                    </div>
                    <div>
                        <strong>Result Date:</strong>
                        <span><?php echo date(DISPLAY_DATE_FORMAT, strtotime($test['result_date'])); ?></span>
                    </div>
                </div>
            </section>
            
            <section class="report-section">
                <h3>Test Results</h3>
                <div class="results-content">
                    <pre><?php echo htmlspecialchars($test['test_results']); ?></pre>
                </div>
            </section>
            
            <section class="report-section">
                <h3>Verification</h3>
                <div class="info-grid">
                    <div>
                        <strong>Performed By:</strong>
                        <span><?php echo htmlspecialchars($test['performed_by']); ?></span>
                    </div>
                    <div>
                        <strong>Verified By:</strong>
                        <span><?php echo htmlspecialchars($test['verified_by']); ?></span>
                    </div>
                    <div>
                        <strong>Verification Date:</strong>
                        <span><?php echo date(DISPLAY_DATETIME_FORMAT, strtotime($test['verification_date'])); ?></span>
                    </div>
                </div>
                <?php if (!empty($test['verification_notes'])): ?>
                    <div class="verification-notes">
                        <strong>Verification Notes:</strong>
                        <p><?php echo htmlspecialchars($test['verification_notes']); ?></p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
        
        <div class="report-footer">
            <p class="text-muted">This is a computer-generated report. No signature is required.</p>
            <p class="text-muted">Generated on <?php echo date(DISPLAY_DATETIME_FORMAT); ?></p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
