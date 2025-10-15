<?php $pageTitle = 'Test Results'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-flask"></i> Test Results</h1>
        <a href="index.php?route=tests/pending" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Tests
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Test Information</h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>Test ID:</dt>
                        <dd><?php echo htmlspecialchars($test['test_id']); ?></dd>
                        
                        <dt>Patient:</dt>
                        <dd><?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?></dd>
                        
                        <dt>Patient ID:</dt>
                        <dd><?php echo htmlspecialchars($test['patient_id']); ?></dd>
                        
                        <dt>Sample ID:</dt>
                        <dd><?php echo htmlspecialchars($test['sample_id']); ?></dd>
                        
                        <dt>Test Name:</dt>
                        <dd><?php echo htmlspecialchars($test['test_name']); ?></dd>
                        
                        <dt>Test Type:</dt>
                        <dd><?php echo ucfirst($test['test_type']); ?></dd>
                        
                        <dt>Priority:</dt>
                        <dd><span class="badge badge-<?php echo $test['priority']; ?>"><?php echo ucfirst($test['priority']); ?></span></dd>
                        
                        <dt>Status:</dt>
                        <dd><span class="badge badge-<?php echo $test['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $test['status'])); ?></span></dd>
                        
                        <dt>Ordered By:</dt>
                        <dd><?php echo htmlspecialchars($test['ordered_by']); ?></dd>
                        
                        <dt>Order Date:</dt>
                        <dd><?php echo date(DISPLAY_DATE_FORMAT, strtotime($test['order_date'])); ?></dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Test Results</h3>
                </div>
                <div class="card-body">
                    <?php if ($test['status'] == TEST_STATUS_ORDERED || $test['status'] == TEST_STATUS_IN_PROGRESS): ?>
                        <form method="POST" action="index.php?route=tests/results&id=<?php echo $test['id']; ?>" class="form">
                            <div class="form-group">
                                <label for="results">Enter Test Results <span class="required">*</span></label>
                                <textarea id="results" name="results" class="form-control" rows="10" required><?php echo htmlspecialchars($test['test_results'] ?? ''); ?></textarea>
                                <small class="form-text">Enter detailed test results, measurements, and observations.</small>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Results
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="results-display">
                            <h4>Results:</h4>
                            <pre><?php echo htmlspecialchars($test['test_results']); ?></pre>
                            
                            <?php if (!empty($test['performed_by'])): ?>
                                <p><strong>Performed By:</strong> <?php echo htmlspecialchars($test['performed_by']); ?></p>
                                <p><strong>Result Date:</strong> <?php echo date(DISPLAY_DATETIME_FORMAT, strtotime($test['result_date'])); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($test['status'] == TEST_STATUS_COMPLETED && hasRole([ROLE_ADMIN, ROLE_DOCTOR])): ?>
                                <hr>
                                <h4>Verify Results</h4>
                                <form method="POST" action="index.php?route=tests/verify" class="form">
                                    <input type="hidden" name="id" value="<?php echo $test['id']; ?>">
                                    <div class="form-group">
                                        <label for="verification_notes">Verification Notes</label>
                                        <textarea id="verification_notes" name="verification_notes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle"></i> Verify Results
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($test['status'] == TEST_STATUS_VERIFIED): ?>
                                <hr>
                                <div class="verification-info">
                                    <h4>Verification Information</h4>
                                    <p><strong>Verified By:</strong> <?php echo htmlspecialchars($test['verified_by']); ?></p>
                                    <p><strong>Verification Date:</strong> <?php echo date(DISPLAY_DATETIME_FORMAT, strtotime($test['verification_date'])); ?></p>
                                    <?php if (!empty($test['verification_notes'])): ?>
                                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($test['verification_notes']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <a href="index.php?route=reports/view&test_id=<?php echo $test['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-file-pdf"></i> Generate Report
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
