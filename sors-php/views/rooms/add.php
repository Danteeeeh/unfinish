<?php
/**
 * Add Room View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Add Operating Room';
?>

<div class="page-header">
    <h1><i class="fas fa-plus"></i> Add Operating Room</h1>
    <div class="header-actions">
        <a href="index.php?route=rooms/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Rooms
        </a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="index.php?route=rooms/add" class="room-form">
        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Room Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="room_number">Room Number *</label>
                    <input type="text" name="room_number" id="room_number" class="form-control" required placeholder="e.g., OR-101">
                </div>

                <div class="form-group">
                    <label for="room_name">Room Name</label>
                    <input type="text" name="room_name" id="room_name" class="form-control" placeholder="e.g., Main Operating Room">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="room_type">Room Type *</label>
                    <select name="room_type" id="room_type" class="form-control" required>
                        <option value="">Select Room Type</option>
                        <?php foreach ($roomTypes as $key => $label): ?>
                            <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="capacity">Capacity (people) *</label>
                    <input type="number" name="capacity" id="capacity" class="form-control" min="1" max="20" value="1" required>
                </div>
            </div>

            <div class="form-group">
                <label for="equipment">Equipment & Facilities</label>
                <textarea name="equipment" id="equipment" class="form-control" rows="4" placeholder="List all equipment and facilities available in this room..."></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Room
            </button>
            <a href="index.php?route=rooms/list" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
/* Add Room Styles */
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
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-control:required {
    border-left: 3px solid var(--primary-color);
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
