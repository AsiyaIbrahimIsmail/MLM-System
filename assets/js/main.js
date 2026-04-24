/**
 * Business Loan Management System - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Hide loading overlay
    setTimeout(() => {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
    }, 500);
});

// Mobile Navigation Toggle
const navToggle = document.getElementById('navToggle');
if (navToggle) {
    navToggle.addEventListener('click', function() {
        const navMenu = document.querySelector('.nav-menu');
        navMenu.classList.toggle('active');
    });
}

// Sidebar Toggle for Dashboard
const sidebarToggle = document.getElementById('sidebarToggle');
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    });
}

// Toast Notifications
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-circle'
    };

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas ${icons[type]} toast-icon"></i>
        <span class="toast-message">${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

    inputs.forEach(input => {
        input.classList.remove('error');
        const errorMsg = input.parentElement.querySelector('.form-error');

        if (!input.value.trim()) {
            input.classList.add('error');
            if (errorMsg) {
                errorMsg.textContent = 'This field is required';
            }
            isValid = false;
        } else if (input.type === 'email' && !isValidEmail(input.value)) {
            input.classList.add('error');
            if (errorMsg) {
                errorMsg.textContent = 'Please enter a valid email';
            }
            isValid = false;
        } else if (input.type === 'tel' && !isValidPhone(input.value)) {
            input.classList.add('error');
            if (errorMsg) {
                errorMsg.textContent = 'Please enter a valid phone number';
            }
            isValid = false;
        }
    });

    return isValid;
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function isValidPhone(phone) {
    const re = /^[\d\s\-\+\(\)]{7,}$/;
    return re.test(phone);
}

// Credit Calculator
function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity')?.value) || 0;
    const unitPrice = parseFloat(document.getElementById('unitPrice')?.value) || 0;
    const total = quantity * unitPrice;

    const totalDisplay = document.getElementById('calculatedTotal');
    if (totalDisplay) {
        totalDisplay.textContent = formatCurrency(total);
    }

    return total;
}

function calculateBalance() {
    const totalDebt = parseFloat(document.getElementById('totalDebt')?.value) || 0;
    const amountPaid = parseFloat(document.getElementById('amountPaid')?.value) || 0;
    const remaining = totalDebt - amountPaid;

    const balanceDisplay = document.getElementById('calculatedBalance');
    if (balanceDisplay) {
        balanceDisplay.textContent = formatCurrency(remaining);
        balanceDisplay.className = remaining > 0 ? 'result-value text-danger' : 'result-value text-success';
    }

    return remaining;
}

function formatCurrency(amount) {
    return '$' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Add event listeners for calculator inputs
document.addEventListener('input', function(e) {
    if (e.target.id === 'quantity' || e.target.id === 'unitPrice') {
        calculateTotal();
    }
    if (e.target.id === 'totalDebt' || e.target.id === 'amountPaid') {
        calculateBalance();
    }
});

// Auto-calculate total for credit sale items
function calculateItemTotal(row) {
    const quantity = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
    const unitPrice = parseFloat(row.querySelector('.item-price')?.value) || 0;
    const subtotal = quantity * unitPrice;

    const subtotalDisplay = row.querySelector('.item-subtotal');
    if (subtotalDisplay) {
        subtotalDisplay.textContent = formatCurrency(subtotal);
    }

    return subtotal;
}

// Calculate grand total
function calculateGrandTotal() {
    const rows = document.querySelectorAll('.credit-sale-item-row');
    let grandTotal = 0;

    rows.forEach(row => {
        grandTotal += calculateItemTotal(row);
    });

    const grandTotalDisplay = document.getElementById('grandTotal');
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = formatCurrency(grandTotal);
    }

    return grandTotal;
}

// Add new item row
function addItemRow() {
    const container = document.getElementById('itemsContainer');
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'credit-sale-item-row';
    row.innerHTML = `
        <div class="form-group">
            <select name="product_id[]" class="form-control form-select item-product" required>
                <option value="">Select Product</option>
            </select>
        </div>
        <div class="form-group">
            <input type="number" name="quantity[]" class="form-control item-quantity" placeholder="Qty" min="1" value="1" required>
        </div>
        <div class="form-group">
            <input type="number" name="unit_price[]" class="form-control item-price" placeholder="Price" step="0.01" min="0" required>
        </div>
        <div class="form-group">
            <span class="item-subtotal">$0.00</span>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    container.appendChild(row);

    // Add event listeners
    row.querySelector('.item-quantity')?.addEventListener('input', () => calculateItemTotal(row));
    row.querySelector('.item-price')?.addEventListener('input', () => calculateItemTotal(row));
    row.querySelector('.item-product')?.addEventListener('change', () => {
        const price = row.querySelector('.item-product');
        if (price) {
            row.querySelector('.item-price').value = price.options[price.selectedIndex]?.dataset?.price || 0;
            calculateItemTotal(row);
        }
    });
}

function removeItemRow(button) {
    const row = button.closest('.credit-sale-item-row');
    if (row) {
        row.remove();
        calculateGrandTotal();
    }
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const activeModal = document.querySelector('.modal-overlay.active');
        if (activeModal) {
            activeModal.classList.remove('active');
        }
    }
});

// Tab functionality
function openTab(tabId) {
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });

    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    document.getElementById(tabId)?.classList.add('active');
    event.target?.classList.add('active');
}

// Search functionality
function setupSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);

    if (!input || !table) return;

    input.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

// Export to CSV
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];

        cols.forEach(col => {
            rowData.push('"' + col.textContent.replace(/"/g, '""') + '"');
        });

        csv.push(rowData.join(','));
    });

    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(csvFile);
    link.download = filename || 'export.csv';
    link.click();
}

// Print-friendly table
function printTable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Report</title>');
    printWindow.document.write('<style>table { border-collapse: collapse; width: 100%; } ');
    printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } ');
    printWindow.document.write('th { background-color: #1e3a5f; color: white; }</style></head><body>');
    printWindow.document.write(table.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Date formatting
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Confirm delete
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;

    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;

    return strength;
}

// Show password strength indicator
function showPasswordStrength(inputId, indicatorId) {
    const input = document.getElementById(inputId);
    const indicator = document.getElementById(indicatorId);

    if (!input || !indicator) return;

    input.addEventListener('input', function() {
        const strength = checkPasswordStrength(this.value);
        const labels = ['Very Weak', 'Weak', 'Fair', 'Strong', 'Very Strong'];
        const colors = ['#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981'];

        indicator.textContent = labels[strength];
        indicator.style.color = colors[strength];
    });
}

// Auto-fill customer balance
function loadCustomerBalance(customerId) {
    if (!customerId) return;

    fetch(`get_customer_balance.php?id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            const balanceDisplay = document.getElementById('customerBalance');
            if (balanceDisplay) {
                balanceDisplay.textContent = formatCurrency(data.balance || 0);
            }
        })
        .catch(error => console.error('Error loading customer balance:', error));
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set up search if inputs exist
    setupSearch('searchInput', 'dataTable');
    setupSearch('searchCustomers', 'customersTable');
    setupSearch('searchProducts', 'productsTable');
    setupSearch('searchSales', 'salesTable');
    setupSearch('searchPayments', 'paymentsTable');

    // Initialize calculators
    calculateItemTotal();
    calculateGrandTotal();

    // Show success/error messages as toasts
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) {
        showToast(decodeURIComponent(urlParams.get('success')), 'success');
    }
    if (urlParams.get('error')) {
        showToast(decodeURIComponent(urlParams.get('error')), 'error');
    }
});

// Utility: Debounce
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

// Utility: Throttle
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}