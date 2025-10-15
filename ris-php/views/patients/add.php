<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php $flash = $this->getFlash(); ?>
<?php if ($flash): ?>
<div class="form-message <?php echo $flash['type']; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<div class="form-container fade-in">
    <h1 class="form-title">Add New Patient</h1>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Patient ID</label>
            <input type="text" name="patient_id" class="form-input" required placeholder="Enter unique patient ID">
        </div>

        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-input" required placeholder="Enter patient's full name">
        </div>

        <div class="form-group">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option value="M">Male</option>
                <option value="F">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" class="form-input" placeholder="Enter phone number">
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" placeholder="Enter email address">
        </div>

        <div class="form-group">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-textarea" placeholder="Enter address"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
                <option value="deceased">Deceased</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add Patient</button>
        <a href="index.php?route=patients/list" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
