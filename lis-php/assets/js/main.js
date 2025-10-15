/**
 * LIS-PHP Main JavaScript
 * Client-side functionality for the Laboratory Information System
 */

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initSearchFunctionality();
    initFormValidation();
    initConfirmDialogs();
    initAutoHideAlerts();
    initTableSorting();
});

/**
 * Search functionality for tables
 */
function initSearchFunctionality() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const table = document.querySelector('.table tbody');
            
            if (table) {
                const rows = table.getElementsByTagName('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    const text = row.textContent.toLowerCase();
                    
                    if (text.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        });
    }
}

/**
 * Form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    
                    // Add error message if not exists
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
                        const errorMsg = document.createElement('span');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = 'var(--danger-color)';
                        errorMsg.style.fontSize = '0.75rem';
                        errorMsg.textContent = 'This field is required';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.classList.remove('error');
                    
                    // Remove error message
                    if (field.nextElementSibling && field.nextElementSibling.classList.contains('error-message')) {
                        field.nextElementSibling.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields', 'error');
            }
        });
        
        // Remove error styling on input
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
                if (this.nextElementSibling && this.nextElementSibling.classList.contains('error-message')) {
                    this.nextElementSibling.remove();
                }
            });
        });
    });
}

/**
 * Confirm dialogs for delete actions
 */
function initConfirmDialogs() {
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    
    deleteLinks.forEach(link => {
        if (!link.hasAttribute('onclick')) {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        }
    });
}

/**
 * Auto-hide alerts after 5 seconds
 */
function initAutoHideAlerts() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
}

/**
 * Table sorting functionality
 */
function initTableSorting() {
    const tables = document.querySelectorAll('.table');
    
    tables.forEach(table => {
        const headers = table.querySelectorAll('th');
        
        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                sortTable(table, index);
            });
        });
    });
}

/**
 * Sort table by column
 */
function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    const sortedRows = rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        // Try to parse as numbers
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return aNum - bNum;
        }
        
        // Compare as strings
        return aValue.localeCompare(bValue);
    });
    
    // Clear tbody
    tbody.innerHTML = '';
    
    // Append sorted rows
    sortedRows.forEach(row => tbody.appendChild(row));
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
        <button type="button" class="close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    const container = document.querySelector('.main-container');
    if (container) {
        container.insertBefore(notification, container.firstChild);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s';
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    }
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return date.toLocaleDateString('en-GB', options);
}

/**
 * Format datetime for display
 */
function formatDateTime(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('en-GB', options);
}

/**
 * Print report
 */
function printReport() {
    window.print();
}

/**
 * Export table to CSV
 */
function exportTableToCSV(filename = 'export.csv') {
    const table = document.querySelector('.table');
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        
        cols.forEach(col => {
            csvRow.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
        });
        
        csv.push(csvRow.join(','));
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

/**
 * Toggle sidebar (for mobile)
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

/**
 * Load patient samples via AJAX
 */
function loadPatientSamples(patientId) {
    fetch(`index.php?route=samples/list&patient_id=${patientId}`)
        .then(response => response.json())
        .then(data => {
            // Update samples list
            console.log('Samples loaded:', data);
        })
        .catch(error => {
            console.error('Error loading samples:', error);
            showNotification('Failed to load samples', 'error');
        });
}

/**
 * Update test status
 */
function updateTestStatus(testId, status) {
    const formData = new FormData();
    formData.append('id', testId);
    formData.append('status', status);
    
    fetch('index.php?route=tests/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Test status updated successfully', 'success');
            location.reload();
        } else {
            showNotification('Failed to update test status', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating test status:', error);
        showNotification('An error occurred', 'error');
    });
}

/**
 * Real-time search with debounce
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export functions for global use
window.showNotification = showNotification;
window.printReport = printReport;
window.exportTableToCSV = exportTableToCSV;
window.toggleSidebar = toggleSidebar;
window.loadPatientSamples = loadPatientSamples;
window.updateTestStatus = updateTestStatus;
