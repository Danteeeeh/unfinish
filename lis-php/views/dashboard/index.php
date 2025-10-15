<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="dashboard-page">
    <h1 class="page-title">Laboratory Information System</h1>

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-box stat-urgent">
            <div class="stat-label">Urgent<br>Results</div>
            <div class="stat-value"><?php echo number_format($stats['pending_tests']); ?></div>
        </div>

        <div class="stat-box stat-pending">
            <div class="stat-label">Pending<br>Tests</div>
            <div class="stat-value"><?php echo number_format($stats['samples_today']); ?></div>
        </div>

        <div class="stat-box stat-processed">
            <div class="stat-label">Samples<br>Processed<br>Today</div>
            <div class="stat-value"><?php echo number_format($stats['completed_today']); ?></div>
        </div>

        <div class="filters-box">
            <div class="filters-label">Quick Filters</div>
            <div class="filter-item" id="urgentFilter" onclick="filterUrgentResults()">
                <span>Urgent results</span>
                <span class="filter-badge" id="urgentBadge"><?php echo $stats['pending_tests']; ?></span>
            </div>
            <div class="filter-item dropdown-filter" onclick="toggleSortDropdown()">
                <span id="sortLabel">Sort By Date</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="sort-dropdown" id="sortDropdown" style="display: none;">
                <div class="sort-option" onclick="applySortFilter('newest')">Newest First</div>
                <div class="sort-option" onclick="applySortFilter('oldest')">Oldest First</div>
                <div class="sort-option" onclick="applySortFilter('status')">By Status</div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="activity-container">
        <div class="activity-header">
            <h2>Recent Laboratory Activity</h2>
            <div class="sort-control">
                <label>Sort by:</label>
                <select id="sortActivity" class="sort-select">
                    <option value="newest">Newest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>
        </div>

        <div class="activity-feed">
            <?php if (empty($recentActivity)): ?>
                <div class="no-activity">
                    <i class="fas fa-inbox"></i>
                    <p>No recent laboratory activity</p>
                </div>
            <?php else: ?>
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-card">
                        <div class="activity-icon-wrapper">
                            <i class="fas <?php echo $activity['icon']; ?>"></i>
                        </div>
                        <div class="activity-info">
                            <div class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></div>
                            <div class="activity-desc"><?php echo htmlspecialchars($activity['description']); ?></div>
                        </div>
                        <div class="activity-menu">
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Filter state
let currentFilter = 'all';
let currentSort = 'newest';

// Update urgent badge count
function updateUrgentBadge() {
    const urgentCount = <?php echo $stats['pending_tests']; ?>;
    document.getElementById('urgentBadge').textContent = urgentCount;
}

// Filter urgent results
function filterUrgentResults() {
    const urgentFilter = document.getElementById('urgentFilter');
    const activityCards = document.querySelectorAll('.activity-card');

    if (currentFilter === 'urgent') {
        // Show all
        currentFilter = 'all';
        urgentFilter.classList.remove('active');
        activityCards.forEach(card => card.style.display = 'flex');
    } else {
        // Show only urgent
        currentFilter = 'urgent';
        urgentFilter.classList.add('active');
        activityCards.forEach(card => {
            const desc = card.querySelector('.activity-desc').textContent.toLowerCase();
            if (desc.includes('urgent') || desc.includes('pending') || desc.includes('stat')) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
}

// Toggle sort dropdown
function toggleSortDropdown() {
    const dropdown = document.getElementById('sortDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// Apply sort filter
function applySortFilter(sortType) {
    currentSort = sortType;
    const activityFeed = document.querySelector('.activity-feed');
    const items = Array.from(activityFeed.querySelectorAll('.activity-card'));

    items.sort((a, b) => {
        const titleA = a.querySelector('.activity-title').textContent;
        const titleB = b.querySelector('.activity-title').textContent;
        const descA = a.querySelector('.activity-desc').textContent;
        const descB = b.querySelector('.activity-desc').textContent;

        if (sortType === 'newest') {
            return titleB.localeCompare(titleA);
        } else if (sortType === 'oldest') {
            return titleA.localeCompare(titleB);
        } else if (sortType === 'status') {
            return descA.localeCompare(descB);
        }
    });

    items.forEach(item => activityFeed.appendChild(item));

    // Update label
    const labels = {
        'newest': 'Newest First',
        'oldest': 'Oldest First',
        'status': 'By Status'
    };
    document.getElementById('sortLabel').textContent = labels[sortType];
    toggleSortDropdown();
}

// Sort activity from select
document.getElementById('sortActivity')?.addEventListener('change', function() {
    applySortFilter(this.value);
});

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('sortDropdown');
    const filterItem = event.target.closest('.dropdown-filter');

    if (!filterItem && dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateUrgentBadge();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
