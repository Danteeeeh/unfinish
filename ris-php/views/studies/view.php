<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Study Details</h1>
<p><strong>Description:</strong> <?php echo htmlspecialchars($study['description']); ?></p>
<p><strong>Patient ID:</strong> <?php echo htmlspecialchars($study['patient_id']); ?></p>
<a href="index.php?route=studies/list">Back to List</a>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
