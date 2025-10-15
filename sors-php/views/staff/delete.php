<?php
/**
 * Delete Staff View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Remove Staff Member';
?>

<div class="page-header">
    <h1><i class="fas fa-trash"></i> Remove Staff Member</h1>
    <div class="header-actions">
        <a href="index.php?route=staff/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Staff
        </a>
    </div>
</div>

<div class="delete-container">
    <div class="delete-confirmation">
        <div class="warning-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h2>Confirm Removal</h2>
        <p>Are you sure you want to remove the following staff member? This action will remove them from the system and cannot be undone.</p>

        <div class="staff-details-card">
            <div class="staff-header">
                <div class="staff-avatar">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="staff-basic-info">
                    <h3><?php echo htmlspecialchars($staffMember['full_name']); ?></h3>
                    <div class="staff-role">
                        <?php echo htmlspecialchars($staffRoles[$staffMember['role']] ?? 'Staff'); ?>
                    </div>
                </div>
            </div>

            <div class="staff-info">
                <div class="info-row">
                    <strong>Employee ID:</strong>
                    <span><?php echo htmlspecialchars($staffMember['employee_id']); ?></span>
                </div>
                <div class="info-row">
                    <strong>Email:</strong>
                    <span><?php echo htmlspecialchars($staffMember['email']); ?></span>
                </div>
                <div class="info-row">
                    <strong>Department:</strong>
                    <span><?php echo htmlspecialchars($staffMember['department'] ?? 'Not specified'); ?></span>
                </div>
                <div class="info-row">
                    <strong>Status:</strong>
                    <span class="status-badge status-<?php echo $staffMember['status'] ?? 'active'; ?>">
                        <?php echo ucfirst($staffMember['status'] ?? 'Active'); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="delete-actions">
            <form method="POST" action="index.php?route=staff/delete&id=<?php echo $staffMember['id']; ?>" style="display: inline;">
                <button type="submit" class="btn btn-danger" onclick="return confirm('This will permanently remove this staff member from the system. Continue?')">
                    <i class="fas fa-trash"></i> Remove Staff Member
                </button>
            </form>
            <a href="index.php?route=staff/list" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </div>
</div>

<style>
/* Delete Staff Styles */
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

.staff-details-card {
    background: var(--bg-secondary);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    text-align: left;
}

.staff-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.staff-avatar {
    width: 50px;
    height: 50px;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    flex-shrink: 0;
}

.staff-basic-info h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.25rem;
    color: var(--text-primary);
}

.staff-role {
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
}

.staff-info {
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

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.status-on_leave {
    background: #fef3c7;
    color: #92400e;
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
