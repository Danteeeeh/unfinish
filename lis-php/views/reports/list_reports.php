<?php $pageTitle = 'Reports List'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-file-medical"></i> Laboratory Reports</h1>
        <a href="index.php?route=reports/generate" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate New Report
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>Completed Tests</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($completedTests)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Patient</th>
                            <th>Test Name</th>
                            <th>Result Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completedTests as $test): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($test['test_id']); ?></td>
                                <td><?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($test['test_name']); ?></td>
                                <td><?php echo date(DISPLAY_DATETIME_FORMAT, strtotime($test['result_date'])); ?></td>
                                <td>
                                    <span class="badge badge-warning">Pending Verification</span>
                                </td>
                                <td>
                                    <a href="index.php?route=tests/results&id=<?php echo $test['id']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View Results
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-muted">No completed tests pending verification.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>Verified Tests (Ready for Report)</h3>
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
                            <th>Verified By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($verifiedTests as $test): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($test['test_id']); ?></td>
                                <td><?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($test['test_name']); ?></td>
                                <td><?php echo date(DISPLAY_DATETIME_FORMAT, strtotime($test['verification_date'])); ?></td>
                                <td><?php echo htmlspecialchars($test['verified_by']); ?></td>
                                <td class="actions">
                                    <a href="index.php?route=reports/view&test_id=<?php echo $test['id']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View Report
                                    </a>
                                    <a href="index.php?route=reports/download&test_id=<?php echo $test['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-muted">No verified tests available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Enhanced Reports List Page Styling */
    .content {
        animation: fadeInUp 0.6s ease;
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 2rem;
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: var(--lab-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-header h1 i {
        color: #667eea;
        font-size: 2rem;
    }

    .card {
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border-color);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .card-header {
        background: var(--lab-gradient);
        color: white;
        padding: 1.5rem 2rem;
    }

    .card-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .card-body {
        padding: 2rem;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .table th {
        background: var(--bg-secondary);
        color: var(--text-primary);
        padding: 1rem;
        text-align: left;
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid var(--border-color);
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        transition: background-color 0.3s ease;
    }

    .table tbody tr {
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .table tbody tr:hover {
        background: rgba(102, 126, 234, 0.05);
        transform: scale(1.01);
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-warning {
        background: var(--warning-gradient);
        color: white;
    }

    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--lab-gradient);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-info {
        background: var(--success-gradient);
        color: white;
    }

    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    .actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .text-center {
        text-align: center;
        color: var(--text-muted);
        padding: 2rem;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .content {
            padding: 1rem;
        }

        .page-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
            padding: 2rem 1.5rem;
        }

        .page-header h1 {
            font-size: 2rem;
            flex-direction: column;
            gap: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .table {
            font-size: 0.875rem;
        }

        .table th,
        .table td {
            padding: 0.75rem 0.5rem;
        }

        .actions {
            flex-direction: column;
            gap: 0.25rem;
        }

        .btn {
            justify-content: center;
            padding: 0.75rem 1rem;
        }
    }

    @media (max-width: 480px) {
        .table {
            overflow-x: auto;
            display: block;
        }

        .table th,
        .table td {
            white-space: nowrap;
        }
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
