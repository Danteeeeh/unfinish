<?php
/**
 * Add Staff View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Add Staff Member';
?>

<div class="page-header">
    <h1><i class="fas fa-plus"></i> Add Staff Member</h1>
    <div class="header-actions">
        <a href="index.php?route=staff/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Staff
        </a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="index.php?route=staff/add" class="staff-form">
        <div class="form-section">
            <h3><i class="fas fa-user"></i> Personal Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="employee_id">Employee ID *</label>
                    <input type="text" name="employee_id" id="employee_id" class="form-control" required placeholder="e.g., EMP001">
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" required placeholder="e.g., Dr. John Smith">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" required placeholder="john.smith@hospital.com">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="+1 (555) 123-4567">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-briefcase"></i> Professional Information</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" class="form-control" placeholder="e.g., Surgery Department">
                </div>

                <div class="form-group">
                    <label for="role">Role *</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">Select Role</option>
                        <?php foreach ($staffRoles as $key => $label): ?>
                            <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input type="text" name="specialization" id="specialization" class="form-control" placeholder="e.g., Cardiac Surgery">
                </div>

                <div class="form-group">
                    <label for="license_number">License Number</label>
                    <input type="text" name="license_number" id="license_number" class="form-control" placeholder="e.g., MD123456">
                </div>
            </div>

            <div class="form-group">
                <label for="experience_years">Years of Experience</label>
                <input type="number" name="experience_years" id="experience_years" class="form-control" min="0" max="50" value="0">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Add Staff Member
            </button>
            <a href="index.php?route=staff/list" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
/* Add Staff Styles */
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
