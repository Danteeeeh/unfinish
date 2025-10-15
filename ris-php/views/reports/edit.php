<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Edit Report</h1>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $report['id']; ?>">
    <label>Title: <input type="text" name="title" value="<?php echo htmlspecialchars($report['title']); ?>" required></label><br>
    <label>Description: <textarea name="description" required><?php echo htmlspecialchars($report['description']); ?></textarea></label><br>
    <label>Patient ID: <input type="number" name="patient_id" value="<?php echo htmlspecialchars($report['patient_id']); ?>" required></label><br>
    <label>Study ID: <input type="number" name="study_id" value="<?php echo htmlspecialchars($report['study_id']); ?>" required></label><br>
    <label>Content: <textarea name="content" rows="10" cols="50" placeholder="Report content..."><?php echo htmlspecialchars($report['content'] ?? ''); ?></textarea></label><br>
    <label>Status: <select name="status">
        <option value="draft" <?php echo $report['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
        <option value="final" <?php echo $report['status'] === 'final' ? 'selected' : ''; ?>>Final</option>
    </select></label><br>
    <button type="submit">Update Report</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
