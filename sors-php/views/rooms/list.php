<?php
/**
 * Rooms List View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Operating Rooms';
?>

<div class="page-header">
    <h1><i class="fas fa-hospital"></i> Operating Rooms</h1>
    <div class="header-actions">
        <a href="index.php?route=rooms/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Room
        </a>
        <a href="index.php?route=rooms/availability" class="btn btn-secondary">
            <i class="fas fa-calendar-check"></i> View Availability
        </a>
    </div>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (empty($rooms)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-hospital"></i>
        </div>
        <h3>No Operating Rooms Found</h3>
        <p>Start by adding your first operating room to manage surgical scheduling.</p>
        <a href="index.php?route=rooms/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add First Room
        </a>
    </div>
<?php else: ?>
    <div class="rooms-grid">
        <?php foreach ($rooms as $room): ?>
            <div class="room-card <?php echo strtolower($room['status']); ?>">
                <div class="room-header">
                    <h3><?php echo htmlspecialchars($room['room_number']); ?></h3>
                    <span class="room-status status-<?php echo $room['status']; ?>">
                        <?php echo ucfirst($room['status']); ?>
                    </span>
                </div>

                <div class="room-details">
                    <h4><?php echo htmlspecialchars($room['room_name'] ?? 'Unnamed Room'); ?></h4>
                    <div class="room-meta">
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>Capacity: <?php echo $room['capacity'] ?? 1; ?> people</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-cog"></i>
                            <span>Type: <?php echo htmlspecialchars($room['room_type'] ?? 'Standard'); ?></span>
                        </div>
                    </div>

                    <?php if (!empty($room['equipment'])): ?>
                        <div class="room-equipment">
                            <h5>Equipment:</h5>
                            <p><?php echo htmlspecialchars($room['equipment']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="room-actions">
                    <a href="index.php?route=rooms/view&id=<?php echo $room['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="index.php?route=rooms/edit&id=<?php echo $room['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="index.php?route=rooms/delete&id=<?php echo $room['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this room?')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
