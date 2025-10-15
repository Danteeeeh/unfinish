<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Users List</h1>
<a href="index.php?route=users/add">Add New User</a>
<ul>
<?php foreach ($users as $user): ?>
    <li>
        <?php echo htmlspecialchars($user['user_id']); ?> - <?php echo htmlspecialchars($user['full_name']); ?> (<?php echo htmlspecialchars($user['role']); ?>)
        <a href="index.php?route=users/edit&id=<?php echo $user['id']; ?>">Edit</a>
        <a href="index.php?route=users/delete&id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
    </li>
<?php endforeach; ?>
</ul>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
