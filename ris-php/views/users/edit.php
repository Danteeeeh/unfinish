<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Edit User</h1>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
    <label>User ID: <input type="text" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>" required></label><br>
    <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></label><br>
    <label>Full Name: <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required></label><br>
    <label>Role: <select name="role">
        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
    </select></label><br>
    <label>Status: <select name="status">
        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
    </select></label><br>
    <button type="submit">Update User</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
