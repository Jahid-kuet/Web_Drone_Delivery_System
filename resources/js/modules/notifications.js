/**
 * =========================================
 * NOTIFICATIONS MODULE
 * =========================================
 * Toast notifications, alerts, and real-time notifications
 */

export function initNotifications() {
    console.log('🔔 Notifications module initialized');
    
    // Initialize notification system
    createNotificationContainer();
    
    // Load unread notifications count
    loadNotificationCount();
    
    // Set up notification polling (every 60 seconds)
    setInterval(loadNotificationCount, 60000);
}

/**
 * Create notification container
 */
function createNotificationContainer() {
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-3';
        container.style.maxWidth = '400px';
        document.body.appendChild(container);
    }
}

/**
 * Show toast notification
 */
export function showToast(message, type = 'info', duration = 3000) {
    const container = document.getElementById('notification-container');
    
    const toast = document.createElement('div');
    toast.className = `notification-toast transform transition-all duration-300 translate-x-full opacity-0 
        flex items-start space-x-3 p-4 rounded-lg shadow-lg ${getToastClasses(type)}`;
    
    toast.innerHTML = `
        <div class="flex-shrink-0">
            <i class="fas ${getToastIcon(type)} text-lg"></i>
        </div>
        <div class="flex-1">
            <p class="font-medium">${message}</p>
        </div>
        <button class="flex-shrink-0 hover:opacity-75" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
    }, 10);
    
    // Auto remove
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

/**
 * Get toast color classes
 */
function getToastClasses(type) {
    const classes = {
        'success': 'bg-green-50 text-green-900 border-l-4 border-green-500',
        'error': 'bg-red-50 text-red-900 border-l-4 border-red-500',
        'warning': 'bg-yellow-50 text-yellow-900 border-l-4 border-yellow-500',
        'info': 'bg-blue-50 text-blue-900 border-l-4 border-blue-500'
    };
    return classes[type] || classes['info'];
}

/**
 * Get toast icon
 */
function getToastIcon(type) {
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    return icons[type] || icons['info'];
}

/**
 * Show confirmation dialog
 */
export function showConfirmDialog(message, onConfirm, onCancel) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Confirm Action</h3>
                </div>
                <p class="text-gray-600 mb-6">${message}</p>
                <div class="flex justify-end space-x-3">
                    <button class="cancel-btn px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                        Cancel
                    </button>
                    <button class="confirm-btn px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle confirm
    modal.querySelector('.confirm-btn').addEventListener('click', () => {
        modal.remove();
        if (onConfirm) onConfirm();
    });
    
    // Handle cancel
    modal.querySelector('.cancel-btn').addEventListener('click', () => {
        modal.remove();
        if (onCancel) onCancel();
    });
    
    // Close on background click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
            if (onCancel) onCancel();
        }
    });
}

/**
 * Load notification count
 */
function loadNotificationCount() {
    const badge = document.getElementById('notification-badge');
    if (!badge) return;
    
    axios.get('/api/notifications/count')
        .then(response => {
            const count = response.data.count || 0;
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Failed to load notification count:', error);
        });
}

/**
 * Show loading overlay
 */
export function showLoading(message = 'Loading...') {
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    
    overlay.innerHTML = `
        <div class="bg-white rounded-lg p-6 flex flex-col items-center space-y-3">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p class="text-gray-700 font-medium">${message}</p>
        </div>
    `;
    
    document.body.appendChild(overlay);
}

/**
 * Hide loading overlay
 */
export function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.remove();
}

// Export for global access
window.showToast = showToast;
window.showConfirmDialog = showConfirmDialog;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
