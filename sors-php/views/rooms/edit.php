<?php
/**
 * Edit Room View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Edit Operating Room';
?>

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Edit Operating Room</h1>
    <div class="header-actions">
        <a href="index.php?route=rooms/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Rooms
        </a>
        <a href="index.php?route=rooms/view&id=<?php echo $room['id']; ?>" class="btn btn-primary">
            <i class="fas fa-eye"></i> View Room
        </a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="index.php?route=rooms/edit&id=<?php echo $room['id']; ?>" class="room-form">
        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Room Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="room_number">Room Number *</label>
                    <input type="text" name="room_number" id="room_number" class="form-control" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="room_name">Room Name</label>
                    <input type="text" name="room_name" id="room_name" class="form-control" value="<?php echo htmlspecialchars($room['room_name'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="room_type">Room Type *</label>
                    <select name="room_type" id="room_type" class="form-control" required>
                        <option value="">Select Room Type</option>
                        <?php foreach ($roomTypes as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($room['room_type'] ?? '') === $key ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="capacity">Capacity (people) *</label>
                    <input type="number" name="capacity" id="capacity" class="form-control" min="1" max="20" value="<?php echo $room['capacity'] ?? 1; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="available" <?php echo ($room['status'] ?? '') === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="occupied" <?php echo ($room['status'] ?? '') === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                        <option value="maintenance" <?php echo ($room['status'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>Under Maintenance</option>
                        <option value="cleaning" <?php echo ($room['status'] ?? '') === 'cleaning' ? 'selected' : ''; ?>>Being Cleaned</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="created_at">Created</label>
                    <input type="text" class="form-control" value="<?php echo date('M j, Y', strtotime($room['created_at'] ?? 'now')); ?>" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="equipment">Equipment & Facilities</label>
                <textarea name="equipment" id="equipment" class="form-control" rows="4"><?php echo htmlspecialchars($room['equipment'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Room
            </button>
            <a href="index.php?route=rooms/list" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
/* Edit Room Styles */
.form-container {
    max-width: 800px;
    margin: 0 auto;
}

.form-section {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
}

.form-section h3 {
    color: var(--text-primary);
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 600;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    background: var(--bg-primary);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-control:required {
    border-left: 3px solid var(--primary-color);
}

.form-control[readonly] {
    background: var(--bg-secondary);
    color: var(--text-secondary);
    cursor: not-allowed;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--border-color);
}

@media (max-width: 768px) {
    .form-container {
        padding: 0 1rem;
    }

    .form-section {
        padding: 1.5rem;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>
