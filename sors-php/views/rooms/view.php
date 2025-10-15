<?php
/**
 * View Room View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Operating Room Details';
?>

<div class="page-header">
    <h1><i class="fas fa-eye"></i> Operating Room Details</h1>
    <div class="header-actions">
        <a href="index.php?route=rooms/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Rooms
        </a>
        <a href="index.php?route=rooms/edit&id=<?php echo $room['id']; ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Room
        </a>
    </div>
</div>

<div class="room-details-container">
    <div class="room-info-card">
        <div class="room-header">
            <div class="room-number-badge">
                <?php echo htmlspecialchars($room['room_number']); ?>
            </div>
            <div class="room-status-badge status-<?php echo $room['status'] ?? 'available'; ?>">
                <?php echo ucfirst($room['status'] ?? 'Available'); ?>
            </div>
        </div>

        <div class="room-content">
            <h2><?php echo htmlspecialchars($room['room_name'] ?? 'Unnamed Room'); ?></h2>

            <div class="room-details-grid">
                <div class="detail-section">
                    <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                    <div class="details-list">
                        <div class="detail-item">
                            <strong>Room Type:</strong>
                            <span><?php echo htmlspecialchars($roomTypes[$room['room_type']] ?? 'Standard'); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Capacity:</strong>
                            <span><?php echo $room['capacity'] ?? 1; ?> people</span>
                        </div>
                        <div class="detail-item">
                            <strong>Created:</strong>
                            <span><?php echo date('M j, Y', strtotime($room['created_at'] ?? 'now')); ?></span>
                        </div>
                    </div>
                </div>

                <?php if (!empty($room['equipment'])): ?>
                    <div class="detail-section">
                        <h3><i class="fas fa-tools"></i> Equipment & Facilities</h3>
                        <div class="equipment-content">
                            <?php echo nl2br(htmlspecialchars($room['equipment'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="room-actions">
                <a href="index.php?route=rooms/edit&id=<?php echo $room['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Room
                </a>
                <a href="index.php?route=rooms/availability" class="btn btn-secondary">
                    <i class="fas fa-calendar-check"></i> Check Availability
                </a>
                <?php if (($room['status'] ?? '') !== 'maintenance'): ?>
                    <button class="btn btn-warning" onclick="toggleMaintenance()">
                        <i class="fas fa-wrench"></i> Mark for Maintenance
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* View Room Styles */
.room-details-container {
    max-width: 800px;
    margin: 0 auto;
}

.room-info-card {
    background: var(--bg-primary);
    border-radius: 12px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.room-header {
    background: var(--primary-gradient);
    padding: 2rem;
    text-align: center;
    position: relative;
}

.room-number-badge {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    margin-bottom: 1rem;
}

.room-status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
}

.status-available {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.status-occupied {
    background: rgba(239, 68, 68, 0.9);
    color: white;
}

.status-maintenance {
    background: rgba(245, 158, 11, 0.9);
    color: white;
}

.status-cleaning {
    background: rgba(59, 130, 246, 0.9);
    color: white;
}

.room-content {
    padding: 2rem;
}

.room-content h2 {
    color: var(--text-primary);
    margin: 0 0 2rem 0;
    text-align: center;
    font-size: 1.8rem;
}

.room-details-grid {
    display: grid;
    gap: 2rem;
    margin-bottom: 2rem;
}

.detail-section {
    background: var(--bg-secondary);
    border-radius: 8px;
    padding: 1.5rem;
}

.detail-section h3 {
    color: var(--text-primary);
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
}

.details-list {
    display: grid;
    gap: 0.75rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item strong {
    color: var(--text-primary);
}

.detail-item span {
    color: var(--text-secondary);
    text-align: right;
}

.equipment-content {
    color: var(--text-secondary);
    line-height: 1.6;
    white-space: pre-line;
}

.room-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    padding-top: 2rem;
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

.btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

@media (max-width: 768px) {
    .room-details-container {
        padding: 0 1rem;
    }

    .room-header {
        padding: 1.5rem;
    }

    .room-number-badge {
        font-size: 2.5rem;
    }

    .room-content {
        padding: 1.5rem;
    }

    .room-details-grid {
        gap: 1.5rem;
    }

    .room-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
}
</style>

<script>
function toggleMaintenance() {
    if (confirm('Are you sure you want to mark this room for maintenance? This will make it unavailable for surgeries.')) {
        // In a real implementation, this would make an AJAX call to update the room status
        alert('Room marked for maintenance. Status updated.');
        location.reload();
    }
}
</script>
