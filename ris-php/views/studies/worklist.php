<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Study Worklist</h1>
<ul>
<?php foreach ($studies as $study): ?>
    <li><?php echo htmlspecialchars($study['description']); ?> - Status: <?php echo htmlspecialchars($study['status']); ?></li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
