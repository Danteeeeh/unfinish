<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php $flash = $this->getFlash(); ?>
<?php if ($flash): ?>
<div class="form-message <?php echo $flash['type']; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<div class="form-container fade-in">
    <h1 class="form-title">Patients List</h1>

    <div style="margin-bottom: 2rem;">
        <a href="index.php?route=patients/add" class="btn btn-primary">Add New Patient</a>
        <a href="index.php?route=patients/search" class="btn btn-secondary">Search Patients</a>
    </div>

    <?php if (empty($patients)): ?>
    <div class="form-message warning">
        No patients found. <a href="index.php?route=patients/add">Add your first patient</a>.
    </div>
    <?php else: ?>
    <div class="table-container">
        <div class="table-header">Patients</div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['id']); ?></td>
                    <td><?php echo htmlspecialchars($patient['patient_id']); ?></td>
                    <td><?php echo htmlspecialchars($patient['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($patient['status'] ?? 'active'); ?></td>
                    <td>
                        <a href="index.php?route=patients/view&id=<?php echo $patient['id']; ?>" class="btn btn-secondary btn-sm">View</a>
                        <a href="index.php?route=patients/edit&id=<?php echo $patient['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="index.php?route=patients/delete&id=<?php echo $patient['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this patient?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
