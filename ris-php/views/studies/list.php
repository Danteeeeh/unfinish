<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Studies List</h1>
<a href="index.php?route=studies/add">Add New Study</a>
<ul>
<?php foreach ($studies as $study): ?>
    <li>
        <?php echo htmlspecialchars($study['description']); ?>
        <a href="index.php?route=studies/view&id=<?php echo $study['id']; ?>">View</a>
        <a href="index.php?route=studies/edit&id=<?php echo $study['id']; ?>">Edit</a>
        <a href="index.php?route=studies/delete&id=<?php echo $study['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
    </li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
