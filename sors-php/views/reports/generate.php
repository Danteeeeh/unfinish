<?php
/**
 * Generate Report View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Generate Report';
?>

<div class="page-header">
    <h1><i class="fas fa-plus"></i> Generate Report</h1>
    <div class="header-actions">
        <a href="index.php?route=reports/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
</div>

<div class="report-form-container">
    <form method="POST" action="index.php?route=reports/generate" class="report-form">
        <div class="form-section">
            <h3><i class="fas fa-chart-line"></i> Report Configuration</h3>

            <div class="form-group">
                <label for="report_type">Report Type</label>
                <select name="type" id="report_type" class="form-control" required>
                    <option value="">Select Report Type</option>
                    <option value="surgery_summary">Surgery Summary</option>
                    <option value="room_utilization">Room Utilization</option>
                    <option value="staff_performance">Staff Performance</option>
                    <option value="patient_outcomes">Patient Outcomes</option>
                    <option value="equipment_usage">Equipment Usage</option>
                </select>
            </div>

            <div class="form-group">
                <label for="report_title">Report Title</label>
                <input type="text" name="title" id="report_title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="date_range">Date Range</label>
                <select name="date_range" id="date_range" class="form-control">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month" selected>This Month</option>
                    <option value="quarter">This Quarter</option>
                    <option value="year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <div id="custom_dates" class="form-group" style="display: none;">
                <div class="date-inputs">
                    <div class="date-input">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control">
                    </div>
                    <div class="date-input">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="report_format">Output Format</label>
                <select name="format" id="report_format" class="form-control">
                    <option value="html" selected>HTML (Web View)</option>
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                    <option value="csv">CSV</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-filter"></i> Filters (Optional)</h3>

            <div class="form-group">
                <label for="room_filter">Operating Room</label>
                <select name="room_id" id="room_filter" class="form-control">
                    <option value="">All Rooms</option>
                    <?php foreach ($rooms ?? [] as $room): ?>
                        <option value="<?php echo $room['id']; ?>"><?php echo htmlspecialchars($room['room_number']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="surgeon_filter">Surgeon</label>
                <select name="surgeon_id" id="surgeon_filter" class="form-control">
                    <option value="">All Surgeons</option>
                    <?php foreach ($surgeons ?? [] as $surgeon): ?>
                        <option value="<?php echo $surgeon['id']; ?>"><?php echo htmlspecialchars($surgeon['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="surgery_type">Surgery Type</label>
                <select name="surgery_type" id="surgery_type" class="form-control">
                    <option value="">All Types</option>
                    <option value="general">General Surgery</option>
                    <option value="orthopedic">Orthopedic Surgery</option>
                    <option value="cardiovascular">Cardiovascular Surgery</option>
                    <option value="neurosurgery">Neurosurgery</option>
                    <option value="plastic">Plastic Surgery</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-cog"></i> Generate Report
            </button>
            <a href="index.php?route=reports/list" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
// Show/hide custom date inputs based on date range selection
document.getElementById('date_range').addEventListener('change', function() {
    const customDates = document.getElementById('custom_dates');
    if (this.value === 'custom') {
        customDates.style.display = 'block';
    } else {
        customDates.style.display = 'none';
    }
});

// Set default dates for custom range
document.getElementById('start_date').valueAsDate = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000); // 30 days ago
document.getElementById('end_date').valueAsDate = new Date(); // Today
</script>
