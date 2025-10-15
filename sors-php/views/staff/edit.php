<?php
/**
 * Edit Staff View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Edit Staff Member';
?>

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Edit Staff Member</h1>
    <div class="header-actions">
        <a href="index.php?route=staff/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Staff
        </a>
        <a href="index.php?route=staff/view&id=<?php echo $staffMember['id']; ?>" class="btn btn-primary">
            <i class="fas fa-eye"></i> View Details
        </a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="index.php?route=staff/edit&id=<?php echo $staffMember['id']; ?>" class="staff-form">
        <div class="form-section">
            <h3><i class="fas fa-user"></i> Personal Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="employee_id">Employee ID *</label>
                    <input type="text" name="employee_id" id="employee_id" class="form-control" value="<?php echo htmlspecialchars($staffMember['employee_id']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($staffMember['full_name']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($staffMember['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($staffMember['phone'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-briefcase"></i> Professional Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" class="form-control" value="<?php echo htmlspecialchars($staffMember['department'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="role">Role *</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">Select Role</option>
                        <?php foreach ($staffRoles as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($staffMember['role'] ?? '') === $key ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input type="text" name="specialization" id="specialization" class="form-control" value="<?php echo htmlspecialchars($staffMember['specialization'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="license_number">License Number</label>
                    <input type="text" name="license_number" id="license_number" class="form-control" value="<?php echo htmlspecialchars($staffMember['license_number'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="experience_years">Years of Experience</label>
                    <input type="number" name="experience_years" id="experience_years" class="form-control" min="0" max="50" value="<?php echo $staffMember['experience_years'] ?? 0; ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" <?php echo ($staffMember['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($staffMember['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="on_leave" <?php echo ($staffMember['status'] ?? '') === 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Staff Member
            </button>
            <a href="index.php?route=staff/list" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
/* Edit Staff Styles */
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
