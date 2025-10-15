<?php $pageTitle = 'Order Test'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-microscope"></i> Order New Test</h1>
        <a href="index.php?route=tests/pending" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Tests
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="index.php?route=tests/order" class="form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="sample_id">Sample <span class="required">*</span></label>
                        <select id="sample_id" name="sample_id" class="form-control" required>
                            <option value="">Select Sample</option>
                            <?php foreach ($samples as $sample): ?>
                                <option value="<?php echo $sample['id']; ?>">
                                    <?php echo htmlspecialchars($sample['order_number'] . ' - ' . $sample['full_name'] . ' (' . ucfirst($sample['status']) . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="test_id">Test <span class="required">*</span></label>
                        <select id="test_id" name="test_id" class="form-control" required>
                            <option value="">Select Test</option>
                            <?php foreach ($availableTests as $test): ?>
                                <option value="<?php echo $test['id']; ?>">
                                    <?php echo htmlspecialchars($test['test_name'] . ' (' . $test['test_code'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="priority">Priority <span class="required">*</span></label>
                        <select id="priority" name="priority" class="form-control" required>
                            <option value="routine">Routine</option>
                            <option value="urgent">Urgent</option>
                            <option value="stat">STAT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes/Instructions</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Special instructions or notes"></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Order Test
                    </button>
                    <a href="index.php?route=tests/pending" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Enhanced Order Test Page Styling */
    .content {
        animation: fadeInUp 0.6s ease;
        max-width: 800px;
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
    }

    .card-body {
        padding: 2.5rem;
    }

    .form {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .required {
        color: #ef4444;
    }

    .form-control {
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    .form-control:hover {
        border-color: #667eea;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    .btn {
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
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

    .btn-secondary {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 2px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--border-color);
        color: var(--text-primary);
        transform: translateY(-2px);
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

        .form-row {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .card-body {
            padding: 1.5rem;
        }

        .form {
            gap: 1.5rem;
        }
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
