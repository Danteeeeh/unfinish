<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Add User</h1>
<form method="POST">
    <label>User ID: <input type="text" name="user_id" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Full Name: <input type="text" name="full_name" required></label><br>
    <label>Role: <select name="role">
        <option value="admin">Admin</option>
        <option value="user" selected>User</option>
    </select></label><br>
    <label>Status: <select name="status">
        <option value="active" selected>Active</option>
        <option value="inactive">Inactive</option>
    </select></label><br>
    <button type="submit">Add User</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
