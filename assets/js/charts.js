/**
 * Business Loan Management System - Dashboard Charts
 * Uses Chart.js for data visualization
 */

// Chart configurations
const chartColors = {
    primary: '#1e3a5f',
    primaryLight: '#2d5a8a',
    secondary: '#10b981',
    secondaryLight: '#34d399',
    warning: '#f59e0b',
    danger: '#ef4444',
    gray: '#6b7280',
    gridLines: '#e5e7eb'
};

// Monthly Credit Chart
function initMonthlyCreditChart(data) {
    const ctx = document.getElementById('monthlyCreditChart');
    if (!ctx) return;

    const defaultData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        credits: [1200, 1900, 1500, 2200, 1800, 2500],
        payments: [800, 1200, 1000, 1500, 1300, 1800]
    };

    const chartData = data || defaultData;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Credit Issued',
                    data: chartData.credits,
                    backgroundColor: chartColors.primary,
                    borderRadius: 4
                },
                {
                    label: 'Payments Received',
                    data: chartData.payments,
                    backgroundColor: chartColors.secondary,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: chartColors.gridLines
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Balance Distribution Chart
function initBalanceChart(data) {
    const ctx = document.getElementById('balanceChart');
    if (!ctx) return;

    const defaultData = {
        labels: ['Total Credit', 'Total Paid', 'Remaining'],
        values: [15000, 10000, 5000]
    };

    const chartData = data || defaultData;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.values,
                backgroundColor: [
                    chartColors.primary,
                    chartColors.secondary,
                    chartColors.warning
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            cutout: '65%'
        }
    });
}

// Customer Balance Chart
function initCustomerBalanceChart(data) {
    const ctx = document.getElementById('customerBalanceChart');
    if (!ctx) return;

    const defaultData = {
        labels: ['John Smith', 'Maria Garcia', 'Robert Johnson', 'Sarah Williams', 'Michael Brown'],
        balances: [65, 0, 60, 55, 0]
    };

    const chartData = data || defaultData;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Remaining Balance',
                data: chartData.balances,
                backgroundColor: chartData.balances.map(b =>
                    b > 0 ? chartColors.warning : chartColors.secondary
                ),
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: chartColors.gridLines
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Payment History Chart
function initPaymentHistoryChart(data) {
    const ctx = document.getElementById('paymentHistoryChart');
    if (!ctx) return;

    const defaultData = {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        values: [2500, 3200, 2800, 4100]
    };

    const chartData = data || defaultData;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Payments',
                data: chartData.values,
                borderColor: chartColors.secondary,
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: chartColors.secondary
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: chartColors.gridLines
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Credit Status Pie Chart
function initCreditStatusChart(data) {
    const ctx = document.getElementById('creditStatusChart');
    if (!ctx) return;

    const defaultData = {
        labels: ['Fully Paid', 'Partially Paid', 'No Payments'],
        values: [5, 8, 2]
    };

    const chartData = data || defaultData;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.values,
                backgroundColor: [
                    chartColors.secondary,
                    chartColors.warning,
                    chartColors.danger
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            }
        }
    });
}

// Initialize all charts
function initCharts() {
    initMonthlyCreditChart();
    initBalanceChart();
    initCustomerBalanceChart();
    initPaymentHistoryChart();
    initCreditStatusChart();
}

// Load chart data from server
function loadChartData(endpoint, callback) {
    fetch(endpoint)
        .then(response => response.json())
        .then(data => callback(data))
        .catch(error => console.error('Error loading chart data:', error));
}

// Update charts with new data
function updateCharts(data) {
    if (data.monthly) initMonthlyCreditChart(data.monthly);
    if (data.balance) initBalanceChart(data.balance);
    if (data.customerBalances) initCustomerBalanceChart(data.customerBalances);
    if (data.payments) initPaymentHistoryChart(data.payments);
    if (data.status) initCreditStatusChart(data.status);
}

// Export chart as image
function exportChart(chartId, filename) {
    const chart = Chart.getChart(chartId);
    if (!chart) return;

    const link = document.createElement('a');
    link.download = filename || 'chart.png';
    link.href = chart.toBase64Image();
    link.click();
}

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('monthlyCreditChart')) {
        initCharts();
    }
});