/**
 * =========================================
 * DASHBOARD MODULE
 * =========================================
 * Real-time statistics, charts, and dashboard interactions
 */

export function initDashboard() {
    console.log('📊 Dashboard module initialized');
    
    // Refresh statistics every 30 seconds
    if (document.querySelector('[data-realtime-stats]')) {
        setInterval(refreshRealtimeStats, 30000);
    }
    
    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initDashboardCharts();
    }
}

/**
 * Refresh real-time statistics
 */
function refreshRealtimeStats() {
    const statsUrl = document.querySelector('[data-realtime-stats]').dataset.realtimeStatsUrl;
    
    axios.get(statsUrl)
        .then(response => {
            updateStatistics(response.data);
        })
        .catch(error => {
            console.error('Failed to refresh stats:', error);
        });
}

/**
 * Update statistics on the page
 */
function updateStatistics(data) {
    // Update total deliveries
    updateStat('total-deliveries', data.total_deliveries);
    updateStat('active-drones', data.active_drones);
    updateStat('pending-requests', data.pending_requests);
    updateStat('total-hospitals', data.total_hospitals);
    
    // Update with animation
    function updateStat(id, value) {
        const element = document.getElementById(id);
        if (element) {
            const current = parseInt(element.textContent) || 0;
            animateNumber(element, current, value, 500);
        }
    }
}

/**
 * Animate number changes
 */
function animateNumber(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16); // 60fps
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.round(current);
    }, 16);
}

/**
 * Initialize dashboard charts
 */
function initDashboardCharts() {
    // Delivery status chart
    const deliveryChartCanvas = document.getElementById('deliveryStatusChart');
    if (deliveryChartCanvas) {
        new Chart(deliveryChartCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'In Transit', 'Preparing', 'Cancelled'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: [
                        '#10b981', // green
                        '#3b82f6', // blue
                        '#f59e0b', // yellow
                        '#ef4444'  // red
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Weekly deliveries chart
    const weeklyChartCanvas = document.getElementById('weeklyDeliveriesChart');
    if (weeklyChartCanvas) {
        new Chart(weeklyChartCanvas, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Deliveries',
                    data: [12, 19, 15, 25, 22, 18, 20],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
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
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Export dashboard refresh function for external use
window.refreshDashboard = refreshRealtimeStats;
