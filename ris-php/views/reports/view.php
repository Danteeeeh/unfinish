<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Report Details</h1>
<div>
    <h3><?php echo htmlspecialchars($report['title']); ?></h3>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($report['description']); ?></p>
    <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($report['patient_id']); ?></p>
    <p><strong>Study ID:</strong> <?php echo htmlspecialchars($report['study_id']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($report['status']); ?></p>
    <?php if (!empty($report['content'])): ?>
    <div><strong>Content:</strong></div>
    <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0; background: #f9f9f9;"><?php echo nl2br(htmlspecialchars($report['content'])); ?></div>
    <?php endif; ?>
    <p><strong>Created:</strong> <?php echo htmlspecialchars($report['created_at']); ?></p>
    <?php if (!empty($report['finalized_at'])): ?>
    <p><strong>Finalized:</strong> <?php echo htmlspecialchars($report['finalized_at']); ?> by User ID: <?php echo htmlspecialchars($report['finalized_by']); ?></p>
    <?php endif; ?>
</div>

<div>
    <a href="index.php?route=reports/edit&id=<?php echo $report['id']; ?>">Edit</a>
    <a href="index.php?route=reports/delete&id=<?php echo $report['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
    <a href="index.php?route=reports/pdf&id=<?php echo $report['id']; ?>">Generate PDF</a>
    <?php if ($report['status'] !== 'final'): ?>
        <a href="index.php?route=reports/finalize&id=<?php echo $report['id']; ?>">Finalize</a>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
