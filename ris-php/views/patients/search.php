<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php $flash = $this->getFlash(); ?>
<?php if ($flash): ?>
<div class="form-message <?php echo $flash['type']; ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
</div>
<?php endif; ?>

<div class="form-container fade-in">
    <h1 class="form-title">Search Patients</h1>

    <form method="GET" style="margin-bottom: 2rem;">
        <input type="hidden" name="route" value="patients/search">
        <div class="form-group">
            <label class="form-label">Search Query</label>
            <input type="text" name="q" class="form-input" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search by name, phone, or email" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="index.php?route=patients/list" class="btn btn-secondary">Back to List</a>
    </form>

    <?php if (empty($patients)): ?>
    <div class="form-message warning">
        No patients found matching your search criteria.
    </div>
    <?php else: ?>
    <div class="table-container">
        <div class="table-header">Search Results (<?php echo count($patients); ?> patients found)</div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient ID</th>
                    <th>Full Name</th>
                    <th>Phone</th>
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
                    <td><?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($patient['status']); ?></td>
                    <td>
                        <a href="index.php?route=patients/view&id=<?php echo $patient['id']; ?>" class="btn btn-secondary btn-sm">View</a>
                        <a href="index.php?route=patients/edit&id=<?php echo $patient['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
