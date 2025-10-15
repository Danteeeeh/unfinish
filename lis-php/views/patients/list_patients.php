<?php $pageTitle = 'Patient List'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-user-injured"></i> Patient Management</h1>
        <a href="index.php?route=patients/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Patient
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>All Patients</h3>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search patients..." class="form-control">
                <button class="btn btn-secondary"><i class="fas fa-search"></i></button>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($patients)): ?>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['patient_id']); ?></td>
                                <td><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></td>
                                <td><?php echo date(DISPLAY_DATE_FORMAT, strtotime($patient['date_of_birth'])); ?></td>
                                <td><?php echo ucfirst($patient['gender']); ?></td>
                                <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                <td class="actions">
                                    <a href="index.php?route=patients/view&id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?route=patients/edit&id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (hasRole([ROLE_ADMIN])): ?>
                                        <a href="index.php?route=patients/delete&id=<?php echo $patient['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this patient?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No patients found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?route=patients/list&page=<?php echo $i; ?>" 
                           class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
