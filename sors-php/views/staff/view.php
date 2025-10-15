<?php
/**
 * View Staff View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Staff Member Details';
?>

<div class="page-header">
    <h1><i class="fas fa-eye"></i> Staff Member Details</h1>
    <div class="header-actions">
        <a href="index.php?route=staff/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Staff
        </a>
        <a href="index.php?route=staff/edit&id=<?php echo $staffMember['id']; ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Staff
        </a>
    </div>
</div>

<div class="staff-details-container">
    <div class="staff-profile-card">
        <div class="staff-header">
            <div class="staff-avatar-large">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="staff-basic-info">
                <h2><?php echo htmlspecialchars($staffMember['full_name']); ?></h2>
                <div class="staff-role-badge">
                    <?php echo htmlspecialchars($staffRoles[$staffMember['role']] ?? 'Staff'); ?>
                </div>
            </div>
        </div>

        <div class="staff-content">
            <div class="staff-details-grid">
                <div class="detail-section">
                    <h3><i class="fas fa-address-card"></i> Contact Information</h3>
                    <div class="details-list">
                        <div class="detail-item">
                            <strong>Employee ID:</strong>
                            <span><?php echo htmlspecialchars($staffMember['employee_id']); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Email:</strong>
                            <span><?php echo htmlspecialchars($staffMember['email']); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Phone:</strong>
                            <span><?php echo htmlspecialchars($staffMember['phone'] ?? 'Not provided'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3><i class="fas fa-briefcase"></i> Professional Information</h3>
                    <div class="details-list">
                        <div class="detail-item">
                            <strong>Department:</strong>
                            <span><?php echo htmlspecialchars($staffMember['department'] ?? 'Not specified'); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Specialization:</strong>
                            <span><?php echo htmlspecialchars($staffMember['specialization'] ?? 'General'); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>License Number:</strong>
                            <span><?php echo htmlspecialchars($staffMember['license_number'] ?? 'Not provided'); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Experience:</strong>
                            <span><?php echo $staffMember['experience_years'] ?? 0; ?> years</span>
                        </div>
                        <div class="detail-item">
                            <strong>Status:</strong>
                            <span class="status-badge status-<?php echo $staffMember['status'] ?? 'active'; ?>">
                                <?php echo ucfirst($staffMember['status'] ?? 'Active'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="staff-actions">
                <a href="index.php?route=staff/edit&id=<?php echo $staffMember['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Staff Member
                </a>
                <a href="index.php?route=staff/schedule" class="btn btn-secondary">
                    <i class="fas fa-calendar"></i> View Schedule
                </a>
                <?php if (($staffMember['status'] ?? '') !== 'inactive'): ?>
                    <button class="btn btn-warning" onclick="toggleStatus()">
                        <i class="fas fa-pause"></i> Deactivate
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* View Staff Styles */
.staff-details-container {
    max-width: 800px;
    margin: 0 auto;
}

.staff-profile-card {
    background: var(--bg-primary);
    border-radius: 12px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.staff-header {
    background: var(--primary-gradient);
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    color: white;
}

.staff-avatar-large {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
}

.staff-basic-info h2 {
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    font-weight: 700;
}

.staff-role-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
}

.staff-content {
    padding: 2rem;
}

.staff-details-grid {
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

.staff-actions {
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
    .staff-details-container {
        padding: 0 1rem;
    }

    .staff-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .staff-avatar-large {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }

    .staff-content {
        padding: 1.5rem;
    }

    .staff-details-grid {
        gap: 1.5rem;
    }

    .staff-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
}
</style>

<script>
function toggleStatus() {
    if (confirm('Are you sure you want to deactivate this staff member? They will not be available for scheduling.')) {
        // In a real implementation, this would make an AJAX call to update the staff status
        alert('Staff member deactivated. Status updated.');
        location.reload();
    }
}
</script>
