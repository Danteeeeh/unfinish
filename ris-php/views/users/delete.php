<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Delete User</h1>
<p>Are you sure you want to delete the user "<?php echo htmlspecialchars($user['username']); ?>"?</p>

<form method="POST" style="display: inline;">
    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
    <button type="submit" onclick="return confirm('This action cannot be undone. Are you sure?')">Yes, Delete</button>
</form>

<a href="index.php?route=users/list">Cancel</a>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
