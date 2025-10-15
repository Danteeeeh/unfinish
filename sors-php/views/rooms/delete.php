<?php
/**
 * Delete Room View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Delete Operating Room';
?>

<div class="page-header">
    <h1><i class="fas fa-trash"></i> Delete Operating Room</h1>
    <div class="header-actions">
        <a href="index.php?route=rooms/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Rooms
        </a>
    </div>
</div>

<div class="delete-container">
    <div class="delete-confirmation">
        <div class="warning-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete the following operating room? This action cannot be undone.</p>

        <div class="room-details-card">
            <h3>Room Details</h3>
            <div class="room-info">
                <div class="info-row">
                    <strong>Room Number:</strong>
                    <span><?php echo htmlspecialchars($room['room_number']); ?></span>
                </div>
                <div class="info-row">
                    <strong>Room Name:</strong>
                    <span><?php echo htmlspecialchars($room['room_name'] ?? 'Unnamed Room'); ?></span>
                </div>
                <div class="info-row">
                    <strong>Type:</strong>
                    <span><?php echo htmlspecialchars($roomTypes[$room['room_type']] ?? 'Standard'); ?></span>
                </div>
                <div class="info-row">
                    <strong>Status:</strong>
                    <span class="status-badge status-<?php echo $room['status'] ?? 'available'; ?>">
                        <?php echo ucfirst($room['status'] ?? 'Available'); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="delete-actions">
            <form method="POST" action="index.php?route=rooms/delete&id=<?php echo $room['id']; ?>" style="display: inline;">
                <button type="submit" class="btn btn-danger" onclick="return confirm('This will permanently delete this operating room. Continue?')">
                    <i class="fas fa-trash"></i> Delete Room
                </button>
            </form>
            <a href="index.php?route=rooms/list" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </div>
</div>

<style>
/* Delete Room Styles */
.delete-container {
    max-width: 600px;
    margin: 0 auto;
}

.delete-confirmation {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid var(--border-color);
    text-align: center;
}

.warning-icon {
    width: 80px;
    height: 80px;
    background: var(--danger-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    font-size: 2rem;
    color: white;
}

.delete-confirmation h2 {
    color: var(--text-primary);
    margin: 0 0 1rem 0;
    font-size: 1.8rem;
}

.delete-confirmation > p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
    line-height: 1.6;
}

.room-details-card {
    background: var(--bg-secondary);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    text-align: left;
}

.room-details-card h3 {
    color: var(--text-primary);
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
}

.room-info {
    display: grid;
    gap: 0.75rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
}

.info-row:last-child {
    border-bottom: none;
}

.info-row strong {
    color: var(--text-primary);
}

.info-row span {
    color: var(--text-secondary);
    text-align: right;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-available {
    background: #d1fae5;
    color: #065f46;
}

.status-occupied {
    background: #fee2e2;
    color: #991b1b;
}

.status-maintenance {
    background: #fef3c7;
    color: #92400e;
}

.status-cleaning {
    background: #dbeafe;
    color: #1e40af;
}

.delete-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
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

.btn-danger {
    background: var(--danger-gradient);
    color: white;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
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
    .delete-container {
        padding: 0 1rem;
    }

    .delete-confirmation {
        padding: 1.5rem;
    }

    .warning-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }

    .delete-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
}
</style>
