<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Reports List</h1>
<a href="index.php?route=reports/create">Create New Report</a>
<ul>
<?php foreach ($reports as $report): ?>
    <li>
        <?php echo htmlspecialchars($report['title']); ?>
        <a href="index.php?route=reports/view&id=<?php echo $report['id']; ?>">View</a>
        <a href="index.php?route=reports/edit&id=<?php echo $report['id']; ?>">Edit</a>
        <a href="index.php?route=reports/delete&id=<?php echo $report['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
    </li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
