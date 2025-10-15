<?php
/**
 * Surgery Calendar View - Content Only
 * This view is included within the main layout
 */

// Set page title
$pageTitle = 'Surgery Calendar';
?>

<div class="page-header">
    <h1><i class="fas fa-calendar-alt"></i> Surgery Calendar</h1>
    <div class="header-actions">
        <a href="index.php?route=surgeries/list" class="btn btn-secondary">
            <i class="fas fa-list"></i> Back to Surgeries
        </a>
        <a href="index.php?route=surgeries/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Schedule Surgery
        </a>
    </div>
</div>

<div class="calendar-container">
    <div class="calendar-header">
        <h3>Operating Room Schedule</h3>
        <div class="calendar-controls">
            <button class="btn btn-secondary" onclick="previousWeek()">Previous Week</button>
            <span class="current-week">This Week</span>
            <button class="btn btn-secondary" onclick="nextWeek()">Next Week</button>
        </div>
    </div>

    <div class="calendar-grid">
        <div class="calendar-rooms">
            <div class="room-header">Operating Rooms</div>
            <div class="room-list">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <div class="room-item">OR-<?php echo $i; ?></div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="calendar-days">
            <div class="days-header">
                <?php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                foreach ($days as $day):
                ?>
                    <div class="day-header"><?php echo $day; ?></div>
                <?php endforeach; ?>
            </div>

            <div class="calendar-slots">
                <?php for ($room = 1; $room <= 6; $room++): ?>
                    <div class="room-slots">
                        <?php for ($day = 1; $day <= 7; $day++): ?>
                            <div class="time-slot" data-room="<?php echo $room; ?>" data-day="<?php echo $day; ?>">
                                <!-- Surgery slots will be populated here -->
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="calendar-legend">
        <h4>Legend:</h4>
        <div class="legend-items">
            <div class="legend-item">
                <div class="legend-color scheduled"></div>
                <span>Scheduled</span>
            </div>
            <div class="legend-item">
                <div class="legend-color in-progress"></div>
                <span>In Progress</span>
            </div>
            <div class="legend-item">
                <div class="legend-color completed"></div>
                <span>Completed</span>
            </div>
        </div>
    </div>
</div>

<style>
/* Calendar Styles */
.calendar-container {
    background: var(--bg-primary);
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid var(--border-color);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.calendar-header h3 {
    color: var(--text-primary);
    margin: 0;
}

.calendar-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.current-week {
    font-weight: 600;
    color: var(--text-primary);
    min-width: 120px;
    text-align: center;
}

.calendar-grid {
    display: grid;
    grid-template-columns: 150px 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
}

.calendar-rooms {
    background: var(--bg-secondary);
    border-radius: 8px;
    padding: 1rem;
}

.room-header {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-color);
}

.room-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.room-item {
    padding: 0.75rem;
    background: var(--bg-primary);
    border-radius: 6px;
    text-align: center;
    font-weight: 500;
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.calendar-days {
    background: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.days-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
}

.day-header {
    padding: 1rem;
    text-align: center;
    font-weight: 600;
    color: var(--text-primary);
    border-right: 1px solid var(--border-color);
}

.day-header:last-child {
    border-right: none;
}

.calendar-slots {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.room-slots {
    display: contents;
}

.time-slot {
    height: 80px;
    border-right: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
    position: relative;
    background: var(--bg-primary);
    transition: background-color 0.3s ease;
}

.time-slot:hover {
    background: var(--bg-secondary);
}

.time-slot:last-child {
    border-right: none;
}

.surgery-block {
    position: absolute;
    top: 2px;
    left: 2px;
    right: 2px;
    bottom: 2px;
    background: var(--primary-gradient);
    border-radius: 4px;
    padding: 0.25rem;
    color: white;
    font-size: 0.75rem;
    cursor: pointer;
    overflow: hidden;
}

.surgery-block.scheduled {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.surgery-block.in-progress {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.surgery-block.completed {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.surgery-block .surgery-title {
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.surgery-block .surgery-time {
    font-size: 0.7rem;
    opacity: 0.9;
}

.calendar-legend {
    background: var(--bg-secondary);
    border-radius: 8px;
    padding: 1rem;
}

.calendar-legend h4 {
    color: var(--text-primary);
    margin: 0 0 1rem 0;
    font-size: 1rem;
}

.legend-items {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 3px;
}

.legend-color.scheduled {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.legend-color.in-progress {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.legend-color.completed {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

@media (max-width: 768px) {
    .calendar-container {
        padding: 1rem;
    }

    .calendar-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .calendar-controls {
        justify-content: center;
    }

    .calendar-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }

    .calendar-rooms {
        order: 2;
    }

    .calendar-days {
        order: 1;
    }

    .room-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
        gap: 0.5rem;
    }

    .room-item {
        padding: 0.5rem;
        font-size: 0.875rem;
    }

    .days-header {
        grid-template-columns: repeat(7, 1fr);
    }

    .day-header {
        padding: 0.5rem;
        font-size: 0.75rem;
    }

    .time-slot {
        height: 60px;
    }

    .surgery-block {
        font-size: 0.7rem;
        padding: 0.2rem;
    }

    .legend-items {
        justify-content: center;
    }
}
</style>

<script>
// Calendar functionality
let currentWeek = new Date();

function formatWeekDate(date) {
    const startOfWeek = new Date(date);
    startOfWeek.setDate(date.getDate() - date.getDay() + 1); // Monday
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6); // Sunday

    return `${startOfWeek.toLocaleDateString()} - ${endOfWeek.toLocaleDateString()}`;
}

function updateWeekDisplay() {
    document.querySelector('.current-week').textContent = formatWeekDate(currentWeek);
    loadCalendarData();
}

function previousWeek() {
    currentWeek.setDate(currentWeek.getDate() - 7);
    updateWeekDisplay();
}

function nextWeek() {
    currentWeek.setDate(currentWeek.getDate() + 7);
    updateWeekDisplay();
}

function loadCalendarData() {
    // Clear existing surgeries
    document.querySelectorAll('.surgery-block').forEach(block => block.remove());

    // Here you would load actual surgery data from the database
    // For now, we'll add some sample data
    addSampleSurgeries();
}

function addSampleSurgeries() {
    // Sample surgery data - in real implementation, this would come from the database
    const sampleSurgeries = [
        {
            id: 1,
            title: 'Appendectomy',
            patient: 'John Doe',
            room: 1,
            day: 1, // Monday
            startHour: 9,
            duration: 2,
            status: 'scheduled'
        },
        {
            id: 2,
            title: 'Knee Surgery',
            patient: 'Jane Smith',
            room: 2,
            day: 2, // Tuesday
            startHour: 14,
            duration: 3,
            status: 'in-progress'
        },
        {
            id: 3,
            title: 'Heart Surgery',
            patient: 'Bob Johnson',
            room: 3,
            day: 4, // Thursday
            startHour: 10,
            duration: 4,
            status: 'completed'
        }
    ];

    sampleSurgeries.forEach(surgery => {
        const slot = document.querySelector(`[data-room="${surgery.room}"][data-day="${surgery.day}"]`);
        if (slot) {
            const surgeryBlock = document.createElement('div');
            surgeryBlock.className = `surgery-block ${surgery.status}`;
            surgeryBlock.innerHTML = `
                <div class="surgery-title">${surgery.title}</div>
                <div class="surgery-time">${surgery.startHour}:00 - ${surgery.startHour + surgery.duration}:00</div>
            `;
            surgeryBlock.title = `${surgery.patient} - ${surgery.title}`;
            slot.appendChild(surgeryBlock);
        }
    });
}

// Initialize calendar
document.addEventListener('DOMContentLoaded', function() {
    updateWeekDisplay();
});
</script>
