<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php $flash = $this->getFlash(); ?>
<?php if ($flash): ?>
<div class="form-message <?php echo $flash['type']; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<div class="form-container fade-in">
    <h1 class="form-title">Patient Details</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($patient['patient_id']); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($patient['full_name']); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($patient['date_of_birth']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($patient['status']); ?></p>
            <?php if (!empty($patient['phone'])): ?>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone']); ?></p>
            <?php endif; ?>
            <?php if (!empty($patient['email'])): ?>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
            <?php endif; ?>
            <?php if (!empty($patient['address'])): ?>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['address']); ?></p>
            <?php endif; ?>
            <p><strong>Created:</strong> <?php echo htmlspecialchars($patient['created_at']); ?></p>
            <p><strong>Last Updated:</strong> <?php echo htmlspecialchars($patient['updated_at']); ?></p>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <a href="index.php?route=patients/edit&id=<?php echo $patient['id']; ?>" class="btn btn-primary">Edit Patient</a>
        <a href="index.php?route=patients/list" class="btn btn-secondary">Back to List</a>
        <a href="index.php?route=patients/delete&id=<?php echo $patient['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this patient?')">Delete Patient</a>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
