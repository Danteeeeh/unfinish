<?php $pageTitle = 'System Settings'; ?>
<?php include __DIR__ . '/../views/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-wrench"></i> System Settings</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php?route=admin" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Admin
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <form method="POST" action="index.php?route=admin/settings">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-cog"></i> General Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="site_name" class="form-label">Site Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name"
                                           value="<?php echo htmlspecialchars($settings['site_name'] ?? 'LIS - Laboratory Information System'); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="site_description" class="form-label">Site Description</label>
                                    <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php
                                        echo htmlspecialchars($settings['site_description'] ?? 'Advanced laboratory management system');
                                    ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode"
                                               <?php echo (isset($settings['maintenance_mode']) && $settings['maintenance_mode']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="maintenance_mode">
                                            Maintenance Mode
                                        </label>
                                    </div>
                                    <div class="form-text">When enabled, only administrators can access the system.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> System Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Version:</strong> 2.0.0
                                </div>
                                <div class="mb-3">
                                    <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?>
                                </div>
                                <div class="mb-3">
                                    <strong>Database:</strong> Connected
                                </div>
                                <div class="mb-3">
                                    <strong>Last Updated:</strong> <?php echo date('M d, Y H:i'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5><i class="fas fa-save"></i> Actions</h5>
                            </div>
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                                <button type="button" class="btn btn-outline-secondary w-100 mb-2" onclick="resetSettings()">
                                    <i class="fas fa-undo"></i> Reset to Default
                                </button>
                                <button type="button" class="btn btn-outline-info w-100" onclick="exportSettings()">
                                    <i class="fas fa-download"></i> Export Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<script>
function resetSettings() {
    if (confirm('Are you sure you want to reset all settings to default?')) {
        document.getElementById('site_name').value = 'LIS - Laboratory Information System';
        document.getElementById('site_description').value = 'Advanced laboratory management system';
        document.getElementById('maintenance_mode').checked = false;
    }
}

function exportSettings() {
    alert('Settings export functionality would be implemented here.');
}
</script>

<?php include __DIR__ . '/../views/layouts/footer.php'; ?>
