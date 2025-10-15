<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Delete Report</h1>
<p>Are you sure you want to delete the report "<?php echo htmlspecialchars($report['title']); ?>"?</p>

<form method="POST" style="display: inline;">
    <input type="hidden" name="id" value="<?php echo $report['id']; ?>">
    <button type="submit" onclick="return confirm('This action cannot be undone. Are you sure?')">Yes, Delete</button>
</form>

<a href="index.php?route=reports/list">Cancel</a>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
