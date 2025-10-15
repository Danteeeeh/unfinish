<?php $pageTitle = 'Edit Prescription'; ?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-prescription"></i> Edit Prescription</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php?module=prescriptions&action=view&id=<?php echo $prescription['id']; ?>" class="btn btn-info me-2">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="index.php?module=prescriptions&action=list" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="card">
                <div class="card-header">
                    <h5>Edit Prescription #<?php echo htmlspecialchars($prescription['prescription_number']); ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?module=prescriptions&action=edit&id=<?php echo $prescription['id']; ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                    <select id="customer_id" name="customer_id" class="form-select" required>
                                        <option value="">Select Customer</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?php echo $customer['id']; ?>"
                                                    <?php echo ($customer['id'] == $prescription['customer_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name'] . ' (' . $customer['patient_id'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="doctor_id" class="form-label">Prescribing Doctor <span class="text-danger">*</span></label>
                                    <select id="doctor_id" name="doctor_id" class="form-select" required>
                                        <option value="">Select Doctor</option>
                                        <?php foreach ($doctors as $doctor): ?>
                                            <option value="<?php echo $doctor['id']; ?>"
                                                    <?php echo ($doctor['id'] == $prescription['doctor_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="medicines" class="form-label">Medicines <span class="text-danger">*</span></label>
                            <textarea id="medicines" name="medicines" class="form-control" rows="4" required><?php echo htmlspecialchars($prescription['medicines']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($prescription['notes'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" name="status" class="form-select">
                                        <option value="pending" <?php echo ($prescription['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="filled" <?php echo ($prescription['status'] == 'filled') ? 'selected' : ''; ?>>Filled</option>
                                        <option value="cancelled" <?php echo ($prescription['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Prescription
                            </button>
                            <a href="index.php?module=prescriptions&action=view&id=<?php echo $prescription['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
