<?php $pageTitle = 'Admin Dashboard'; ?>
<?php include __DIR__ . '/../views/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-cog"></i> Admin Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php?route=admin/settings" class="btn btn-outline-primary me-2">
                        <i class="fas fa-wrench"></i> System Settings
                    </a>
                    <a href="index.php?route=admin/users" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-users"></i> User Management
                    </a>
                    <a href="index.php?route=admin/future" class="btn btn-outline-info">
                        <i class="fas fa-rocket"></i> Future Features
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <!-- System Overview -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo number_format($stats['total_patients']); ?></h5>
                            <p class="card-text">Total Patients</p>
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?php echo number_format($stats['total_samples']); ?></h5>
                            <p class="card-text">Total Samples</p>
                            <i class="fas fa-vial fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-warning"><?php echo number_format($stats['pending_tests']); ?></h5>
                            <p class="card-text">Pending Tests</p>
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info"><?php echo number_format($stats['completed_today']); ?></h5>
                            <p class="card-text">Completed Today</p>
                            <i class="fas fa-check-circle fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-line"></i> System Overview</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="systemChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-info-circle"></i> System Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="badge bg-success">Database: Connected</span>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-info">System: Online</span>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-warning">Maintenance: Scheduled</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Last Backup: Today 2:00 AM</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// System Overview Chart
const ctx = document.getElementById('systemChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Tests Processed',
            data: [120, 150, 180, 200, 240, 280],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'New Patients',
            data: [80, 100, 120, 140, 160, 180],
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});
</script>

<?php include __DIR__ . '/../views/layouts/footer.php'; ?>
