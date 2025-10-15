// SORS - Surgery Operating Room Scheduling System JavaScript

// Theme Management
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('sors_theme', newTheme);

    // Update toggle button
    const toggleBall = document.querySelector('.toggle-ball');
    if (newTheme === 'dark') {
        toggleBall.style.transform = 'translateY(-50%) translateX(25px)';
    } else {
        toggleBall.style.transform = 'translateY(-50%) translateX(0)';
    }

    // Show notification
    showNotification(`Switched to ${newTheme} mode!`, 'success');
}

function initializeTheme() {
    const savedTheme = localStorage.getItem('sors_theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Update toggle button on load
    const toggleBall = document.querySelector('.toggle-ball');
    if (savedTheme === 'dark') {
        toggleBall.style.transform = 'translateY(-50%) translateX(25px)';
    }
}

// Notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const colors = {
        success: 'var(--success-gradient)',
        error: 'var(--danger-gradient)',
        warning: 'var(--warning-gradient)',
        info: 'var(--primary-gradient)'
    };

    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${colors[type]};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        z-index: 10001;
        max-width: 350px;
        font-family: 'Inter', sans-serif;
        animation: slideInRight 0.3s ease;
    `;

    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-${getIcon(type)}" style="font-size: 18px;"></i>
            <p style="margin: 0; font-size: 14px; line-height: 1.4;">${message}</p>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(() => notification.parentNode.removeChild(notification), 300);
        }
    }, 4000);
}

function getIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Sidebar Management
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');

    if (sidebar && menuToggle) {
        sidebar.classList.toggle('show');
        const icon = menuToggle.querySelector('i');

        if (sidebar.classList.contains('show')) {
            icon.className = 'fas fa-times';
        } else {
            icon.className = 'fas fa-bars';
        }
    }
}

function initializeSidebar() {
    const menuToggle = document.getElementById('menuToggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }

    // Set active nav item based on current URL
    const currentPath = window.location.href;
    document.querySelectorAll('.nav-item').forEach(item => {
        if (item.href === currentPath) {
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        }
    });
}

// Form Validation
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            showNotification(`${field.name || 'Field'} is required`, 'error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });

    return isValid;
}

// Confirmation Dialogs
function confirmAction(message = 'Are you sure you want to proceed?') {
    return confirm(message);
}

// AJAX Helper
async function ajaxRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    const finalOptions = { ...defaultOptions, ...options };

    if (finalOptions.body && typeof finalOptions.body === 'object') {
        finalOptions.body = JSON.stringify(finalOptions.body);
    }

    try {
        const response = await fetch(url, finalOptions);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        showNotification('Request failed: ' + error.message, 'error');
        throw error;
    }
}

// Date Helpers
function formatDate(date, format = 'YYYY-MM-DD') {
    const d = new Date(date);

    const formats = {
        'YYYY-MM-DD': d.toISOString().split('T')[0],
        'DD/MM/YYYY': `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`,
        'MM/DD/YYYY': `${String(d.getMonth() + 1).padStart(2, '0')}/${String(d.getDate()).padStart(2, '0')}/${d.getFullYear()}`,
        'MMM DD, YYYY': d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    };

    return formats[format] || d.toISOString().split('T')[0];
}

function formatTime(time, format = '24h') {
    const [hours, minutes] = time.split(':');
    const date = new Date();
    date.setHours(parseInt(hours), parseInt(minutes));

    if (format === '12h') {
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    return time;
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
    initializeSidebar();

    // Add notification styles
    if (!document.getElementById('notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slideOutRight {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(100%);
                }
            }

            .form-control.error {
                border-color: #ef4444;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            }
        `;
        document.head.appendChild(styles);
    }

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (alert.querySelector('.btn-close')) {
                alert.style.animation = 'slideOutRight 0.3s ease forwards';
                setTimeout(() => alert.remove(), 300);
            }
        });
    }, 5000);
});

// Export functions for use in other scripts
window.SORS = {
    toggleTheme,
    showNotification,
    toggleSidebar,
    validateForm,
    confirmAction,
    ajaxRequest,
    formatDate,
    formatTime
};
