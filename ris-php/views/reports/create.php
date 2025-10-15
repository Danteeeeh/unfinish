<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Create Report</h1>
<form method="POST">
    <label>Title: <input type="text" name="title" required></label><br>
    <label>Description: <textarea name="description" required></textarea></label><br>
    <label>Patient ID: <input type="number" name="patient_id" required></label><br>
    <label>Study ID: <input type="number" name="study_id" required></label><br>
    <label>Content: <textarea name="content" rows="10" cols="50" placeholder="Report content..."></textarea></label><br>
    <label>Status: <select name="status"><option value="draft">Draft</option><option value="final">Final</option></select></label><br>
    <button type="submit">Create Report</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
