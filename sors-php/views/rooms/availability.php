<?php
/**
 * Room Availability View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Room Availability';
?>

<div class="page-header">
    <h1><i class="fas fa-calendar-check"></i> Room Availability</h1>
    <div class="header-actions">
        <a href="index.php?route=rooms/list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Rooms
        </a>
    </div>
</div>

<div class="availability-container">
    <div class="availability-summary">
        <div class="summary-grid">
            <div class="summary-item available">
                <div class="summary-number"><?php echo $availability['available'] ?? 0; ?></div>
                <div class="summary-label">Available</div>
            </div>
            <div class="summary-item occupied">
                <div class="summary-number"><?php echo $availability['occupied'] ?? 0; ?></div>
                <div class="summary-label">Occupied</div>
            </div>
            <div class="summary-item maintenance">
                <div class="summary-number"><?php echo $availability['maintenance'] ?? 0; ?></div>
                <div class="summary-label">Maintenance</div>
            </div>
            <div class="summary-item cleaning">
                <div class="summary-number"><?php echo $availability['cleaning'] ?? 0; ?></div>
                <div class="summary-label">Cleaning</div>
            </div>
        </div>
    </div>

    <div class="availability-details">
        <h3>Room Status Overview</h3>
        <div class="status-legend">
            <div class="legend-item">
                <div class="legend-color available"></div>
                <span>Available - Ready for surgeries</span>
            </div>
            <div class="legend-item">
                <div class="legend-color occupied"></div>
                <span>Occupied - Currently in use</span>
            </div>
            <div class="legend-item">
                <div class="legend-color maintenance"></div>
                <span>Maintenance - Under repair</span>
            </div>
            <div class="legend-item">
                <div class="legend-color cleaning"></div>
                <span>Cleaning - Being cleaned</span>
            </div>
        </div>

        <div class="availability-actions">
            <button class="btn btn-primary" onclick="refreshAvailability()">
                <i class="fas fa-sync"></i> Refresh Status
            </button>
            <a href="index.php?route=rooms/list" class="btn btn-secondary">
                <i class="fas fa-list"></i> View All Rooms
            </a>
        </div>
    </div>
</div>

<style>
/* Room Availability Styles */
.availability-container {
    max-width: 1000px;
    margin: 0 auto;
}

.availability-summary {
    margin-bottom: 2rem;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.summary-item {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.summary-item.available {
    border-left: 4px solid #10b981;
}

.summary-item.occupied {
    border-left: 4px solid #ef4444;
}

.summary-item.maintenance {
    border-left: 4px solid #f59e0b;
}

.summary-item.cleaning {
    border-left: 4px solid #3b82f6;
}

.summary-number {
    font-size: 3rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.summary-label {
    color: var(--text-secondary);
    font-size: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.availability-details {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid var(--border-color);
}

.availability-details h3 {
    color: var(--text-primary);
    margin: 0 0 1.5rem 0;
    font-size: 1.5rem;
}

.status-legend {
    margin-bottom: 2rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding: 1rem;
    background: var(--bg-secondary);
    border-radius: 8px;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    flex-shrink: 0;
}

.legend-color.available {
    background: #10b981;
}

.legend-color.occupied {
    background: #ef4444;
}

.legend-color.maintenance {
    background: #f59e0b;
}

.legend-color.cleaning {
    background: #3b82f6;
}

.availability-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    padding-top: 1.5rem;
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
    .availability-container {
        padding: 0 1rem;
    }

    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .availability-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
}

@media (max-width: 480px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function refreshAvailability() {
    // In a real implementation, this would make an AJAX call to refresh the availability data
    location.reload();
}
</script>
