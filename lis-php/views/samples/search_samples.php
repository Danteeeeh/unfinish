<?php $pageTitle = 'Search Samples'; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="content">
    <div class="page-header">
        <h1><i class="fas fa-search"></i> Search Samples</h1>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="GET" action="index.php" class="search-form">
                <input type="hidden" name="route" value="samples/search">
                <div class="form-row">
                    <div class="form-group flex-grow">
                        <input type="text" name="keyword" class="form-control" 
                               placeholder="Search by Sample ID, Patient Name, or Sample Type..." 
                               value="<?php echo htmlspecialchars($keyword); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (!empty($keyword)): ?>
        <div class="card">
            <div class="card-header">
                <h3>Search Results (<?php echo count($results); ?> found)</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($results)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sample ID</th>
                                <th>Patient</th>
                                <th>Sample Type</th>
                                <th>Collection Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $sample): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sample['sample_id']); ?></td>
                                    <td><?php echo htmlspecialchars($sample['first_name'] . ' ' . $sample['last_name']); ?></td>
                                    <td><?php echo ucfirst($sample['sample_type']); ?></td>
                                    <td><?php echo date(DISPLAY_DATE_FORMAT, strtotime($sample['collection_date'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $sample['status']; ?>">
                                            <?php echo ucfirst($sample['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="index.php?route=samples/status&id=<?php echo $sample['id']; ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-muted">No samples found matching your search criteria.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
