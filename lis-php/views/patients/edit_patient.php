<?php $pageTitle = 'Edit Patient'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-user-edit"></i> Edit Patient</h1>
        <a href="index.php?route=patients/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="index.php?route=patients/edit&id=<?php echo $patient['id']; ?>" class="form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" class="form-control" 
                               value="<?php echo htmlspecialchars($patient['first_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" class="form-control" 
                               value="<?php echo htmlspecialchars($patient['last_name']); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth <span class="required">*</span></label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" 
                               value="<?php echo $patient['date_of_birth']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">Gender <span class="required">*</span></label>
                        <select id="gender" name="gender" class="form-control" required>
                            <option value="male" <?php echo ($patient['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($patient['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($patient['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($patient['email']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" class="form-control" 
                               value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($patient['address']); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="emergency_contact">Emergency Contact Name</label>
                        <input type="text" id="emergency_contact" name="emergency_contact" class="form-control" 
                               value="<?php echo htmlspecialchars($patient['emergency_contact']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="emergency_phone">Emergency Contact Phone</label>
                        <input type="tel" id="emergency_phone" name="emergency_phone" class="form-control" 
                               value="<?php echo htmlspecialchars($patient['emergency_phone']); ?>">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Patient
                    </button>
                    <a href="index.php?route=patients/list" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
