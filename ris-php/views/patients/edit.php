<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php $flash = $this->getFlash(); ?>
<?php if ($flash): ?>
<div class="form-message <?php echo $flash['type']; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<div class="form-container fade-in">
    <h1 class="form-title">Edit Patient</h1>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $patient['id']; ?>">

        <div class="form-group">
            <label class="form-label">Patient ID</label>
            <input type="text" name="patient_id" class="form-input" value="<?php echo htmlspecialchars($patient['patient_id']); ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-input" value="<?php echo htmlspecialchars($patient['full_name']); ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-input" value="<?php echo htmlspecialchars($patient['date_of_birth']); ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option value="M" <?php echo $patient['gender'] === 'M' ? 'selected' : ''; ?>>Male</option>
                <option value="F" <?php echo $patient['gender'] === 'F' ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo $patient['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars($patient['phone'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($patient['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-textarea"><?php echo htmlspecialchars($patient['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" <?php echo $patient['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $patient['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                <option value="deceased" <?php echo $patient['status'] === 'deceased' ? 'selected' : ''; ?>>Deceased</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Patient</button>
        <a href="index.php?route=patients/list" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
