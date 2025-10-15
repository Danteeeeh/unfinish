<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Add Study</h1>
<form method="POST">
    <label>Description: <input type="text" name="description" required></label><br>
    <label>Patient ID: <input type="number" name="patient_id" required></label><br>
    <button type="submit">Add Study</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
