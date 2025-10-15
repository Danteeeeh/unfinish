<?php
/**
 * Staff List View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Staff Management';
?>

<div class="page-header">
    <h1><i class="fas fa-users"></i> Staff Management</h1>
    <div class="header-actions">
        <a href="index.php?route=staff/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Staff
        </a>
        <a href="index.php?route=staff/schedule" class="btn btn-secondary">
            <i class="fas fa-calendar"></i> View Schedule
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

<?php if (empty($staff)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-users"></i>
        </div>
        <h3>No Staff Members Found</h3>
        <p>Start by adding your first staff member to manage surgical teams.</p>
        <a href="index.php?route=staff/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add First Staff Member
        </a>
    </div>
<?php else: ?>
    <div class="staff-grid">
        <?php foreach ($staff as $member): ?>
            <div class="staff-card">
                <div class="staff-avatar">
                    <i class="fas fa-user-md"></i>
                </div>

                <div class="staff-info">
                    <h3><?php echo htmlspecialchars($member['full_name']); ?></h3>
                    <div class="staff-role">
                        <span class="role-badge <?php echo strtolower(str_replace(' ', '-', $member['role'] ?? 'staff')); ?>">
                            <?php echo htmlspecialchars($member['role'] ?? 'Staff'); ?>
                        </span>
                    </div>

                    <div class="staff-details">
                        <div class="detail-item">
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($member['email'] ?? 'No email'); ?>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-phone"></i>
                            <?php echo htmlspecialchars($member['phone'] ?? 'No phone'); ?>
                        </div>
                        <?php if (!empty($member['department'])): ?>
                            <div class="detail-item">
                                <i class="fas fa-building"></i>
                                <?php echo htmlspecialchars($member['department']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($member['specialization'])): ?>
                            <div class="detail-item">
                                <i class="fas fa-stethoscope"></i>
                                <?php echo htmlspecialchars($member['specialization']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="staff-actions">
                    <a href="index.php?route=staff/view&id=<?php echo $member['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="index.php?route=staff/edit&id=<?php echo $member['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="index.php?route=staff/delete&id=<?php echo $member['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this staff member?')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
