<?php $pageTitle = 'Sample Management'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="samples-page">
    <div class="samples-header">
        <h1 class="page-title">SAMPLES</h1>
        <button class="btn btn-primary" onclick="location.href='index.php?route=samples/add'">
            <i class="fas fa-plus"></i> Add new sample
        </button>
    </div>

    <!-- Search and Filters -->
    <div class="samples-controls">
        <div class="search-box">
            <input type="text" id="sampleSearch" placeholder="Search samples..." class="search-input">
            <button class="search-btn" onclick="searchSamples()">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <div class="status-filters">
            <span class="filter-label">Status:</span>
            <label class="status-checkbox">
                <input type="radio" name="statusFilter" value="all" checked onchange="filterByStatus(this.value)">
                <span class="status-dot status-all"></span> All
            </label>
            <label class="status-checkbox">
                <input type="radio" name="statusFilter" value="pending" onchange="filterByStatus(this.value)">
                <span class="status-dot status-registered"></span> Registered
            </label>
            <label class="status-checkbox">
                <input type="radio" name="statusFilter" value="processing" onchange="filterByStatus(this.value)">
                <span class="status-dot status-progress"></span> In Progress
            </label>
            <label class="status-checkbox">
                <input type="radio" name="statusFilter" value="completed" onchange="filterByStatus(this.value)">
                <span class="status-dot status-completed"></span> Completed
            </label>
        </div>
    </div>

    <!-- Column Filters -->
    <div class="column-filters">
        <label class="column-checkbox">
            <input type="checkbox" checked onchange="toggleColumn('patient')"> Patient Name
        </label>
        <label class="column-checkbox">
            <input type="checkbox" checked onchange="toggleColumn('patientId')"> Patient ID
        </label>
        <label class="column-checkbox">
            <input type="checkbox" checked onchange="toggleColumn('testType')"> Test Type
        </label>
        <label class="column-checkbox">
            <input type="checkbox" checked onchange="toggleColumn('dateReceived')"> Date received
        </label>
        <label class="column-checkbox">
            <input type="checkbox" checked onchange="toggleColumn('status')"> Status
        </label>
    </div>

    <!-- Samples Table -->
    <div class="samples-table-container">
        <table class="samples-table" id="samplesTable">
            <thead>
                <tr>
                    <th class="col-sampleId">Sample ID</th>
                    <th class="col-patient">Patient Name</th>
                    <th class="col-patientId">Patient ID</th>
                    <th class="col-testType">Test Type</th>
                    <th class="col-dateReceived">Date Received</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($samples)): ?>
                    <?php foreach ($samples as $sample): ?>
                        <tr class="sample-row" data-status="<?php echo $sample['status']; ?>">
                            <td class="col-sampleId"><?php echo htmlspecialchars($sample['sample_id']); ?></td>
                            <td class="col-patient"><?php echo htmlspecialchars($sample['first_name'] . ' ' . $sample['last_name']); ?></td>
                            <td class="col-patientId"><?php echo htmlspecialchars($sample['patient_id'] ?? 'N/A'); ?></td>
                            <td class="col-testType"><?php echo ucfirst($sample['sample_type']); ?></td>
                            <td class="col-dateReceived"><?php echo date('d/m/Y', strtotime($sample['collection_date'])); ?></td>
                            <td class="col-status">
                                <span class="status-badge status-<?php echo $sample['status']; ?>">
                                    <?php echo ucfirst($sample['status']); ?>
                                </span>
                            </td>
                            <td class="col-actions">
                                <button class="action-btn" onclick="viewSample(<?php echo $sample['id']; ?>)" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn" onclick="editSample(<?php echo $sample['id']; ?>)" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteSample(<?php echo $sample['id']; ?>)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No samples found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner"></div>
        </div>
    </div>

    <style>
        /* Enhanced Samples Page Styling */
        .samples-page {
            animation: fadeInUp 0.6s ease;
        }

        .samples-header {
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

        .samples-controls {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .search-box {
            display: flex;
            flex: 1;
            max-width: 400px;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px 0 0 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .search-btn {
            padding: 0.75rem 1.5rem;
            background: var(--lab-gradient);
            color: white;
            border: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .search-btn:hover {
            transform: scale(1.05);
        }

        .status-filters {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .filter-label {
            font-weight: 600;
            color: var(--text-primary);
        }

        .status-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .status-checkbox:hover {
            transform: translateY(-2px);
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-all {
            background: var(--lab-gradient);
        }

        .status-registered {
            background: var(--warning-gradient);
        }

        .status-progress {
            background: var(--primary-gradient);
        }

        .status-completed {
            background: var(--success-gradient);
        }

        .column-filters {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            flex-wrap: wrap;
        }

        .column-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .column-checkbox:hover {
            color: #667eea;
        }

        .samples-table-container {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
        }

        .samples-table {
            width: 100%;
            border-collapse: collapse;
        }

        .samples-table th {
            background: var(--lab-gradient);
            color: white;
            padding: 1.5rem 1rem;
            text-align: left;
            font-weight: 700;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .samples-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.3s ease;
        }

        .sample-row {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sample-row:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            animation: pulse 2s infinite;
        }

        .status-registered {
            background: var(--warning-gradient);
            color: white;
        }

        .status-progress {
            background: var(--primary-gradient);
            color: white;
        }

        .status-completed {
            background: var(--success-gradient);
            color: white;
        }

        .action-btn {
            padding: 0.5rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
        }

        .action-btn:hover {
            background: var(--lab-gradient);
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .delete-btn:hover {
            background: var(--danger-gradient);
            border-color: #ef4444;
        }

        .text-center {
            text-align: center;
            color: var(--text-muted);
            padding: 2rem;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .samples-header {
                flex-direction: column;
                gap: 1rem;
                padding: 1.5rem;
            }

            .samples-controls {
                flex-direction: column;
                gap: 1rem;
            }

            .status-filters {
                flex-wrap: wrap;
            }

            .column-filters {
                flex-direction: column;
                gap: 1rem;
            }

            .samples-table {
                font-size: 0.875rem;
            }

            .samples-table th,
            .samples-table td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</div>

<script>
// Show loading spinner
function showSpinner() {
    document.getElementById('loadingSpinner').style.display = 'block';
}

// Hide loading spinner
function hideSpinner() {
    document.getElementById('loadingSpinner').style.display = 'none';
}
// Search samples
function searchSamples() {
    const searchTerm = document.getElementById('sampleSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.sample-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Real-time search
document.getElementById('sampleSearch')?.addEventListener('input', searchSamples);

// Filter by status
function filterByStatus(status) {
    const rows = document.querySelectorAll('.sample-row');
    
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Toggle column visibility
function toggleColumn(columnName) {
    const columns = document.querySelectorAll(`.col-${columnName}`);
    columns.forEach(col => {
        col.style.display = col.style.display === 'none' ? '' : 'none';
    });
}

// View sample
function viewSample(id) {
    window.location.href = `index.php?route=samples/status&id=${id}`;
}

// Edit sample
function editSample(id) {
    window.location.href = `index.php?route=samples/edit&id=${id}`;
}

// Delete sample
function deleteSample(id) {
    if (confirm('Are you sure you want to delete this sample?')) {
        window.location.href = `index.php?route=samples/delete&id=${id}`;
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
