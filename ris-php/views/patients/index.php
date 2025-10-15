<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="form-container fade-in">
    <h1 class="form-title">Patients Management</h1>

    <div class="quick-actions" style="max-width: 600px; margin: 0 auto;">
        <a href="index.php?route=patients/list" class="action-card">
            <h4><i class="fas fa-list"></i> View All Patients</h4>
            <p>Browse and manage all registered patients</p>
        </a>
        <a href="index.php?route=patients/add" class="action-card">
            <h4><i class="fas fa-plus"></i> Add New Patient</h4>
            <p>Register a new patient in the system</p>
        </a>
        <a href="index.php?route=patients/search" class="action-card">
            <h4><i class="fas fa-search"></i> Search Patients</h4>
            <p>Find patients by name, ID, or other criteria</p>
        </a>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
