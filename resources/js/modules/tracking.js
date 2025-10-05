/**
 * =========================================
 * DELIVERY TRACKING MODULE
 * =========================================
 * Real-time delivery tracking and status updates
 */

export function initTracking() {
    console.log('📍 Tracking module initialized');
    
    // Auto-refresh tracking every 10 seconds
    const trackingContainer = document.querySelector('[data-tracking-number]');
    if (trackingContainer) {
        const trackingNumber = trackingContainer.dataset.trackingNumber;
        setInterval(() => refreshTrackingStatus(trackingNumber), 10000);
    }
    
    // Initialize map if available
    if (typeof L !== 'undefined') { // Leaflet.js
        initTrackingMap();
    }
}

/**
 * Refresh tracking status
 */
function refreshTrackingStatus(trackingNumber) {
    axios.get(`/api/tracking/${trackingNumber}`)
        .then(response => {
            updateTrackingDisplay(response.data);
        })
        .catch(error => {
            console.error('Failed to refresh tracking:', error);
        });
}

/**
 * Update tracking display
 */
function updateTrackingDisplay(data) {
    // Update status badge
    const statusElement = document.getElementById('delivery-status');
    if (statusElement) {
        statusElement.textContent = data.status;
        statusElement.className = getStatusClass(data.status);
    }
    
    // Update location
    const locationElement = document.getElementById('current-location');
    if (locationElement && data.current_location) {
        locationElement.textContent = data.current_location;
    }
    
    // Update estimated time
    const etaElement = document.getElementById('estimated-delivery');
    if (etaElement && data.estimated_delivery_time) {
        etaElement.textContent = formatDateTime(data.estimated_delivery_time);
    }
    
    // Update map marker if map is initialized
    if (window.trackingMap && data.latitude && data.longitude) {
        updateMapMarker(data.latitude, data.longitude);
    }
}

/**
 * Get status CSS class
 */
function getStatusClass(status) {
    const classes = {
        'preparing': 'px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full',
        'in_transit': 'px-4 py-2 bg-blue-100 text-blue-800 rounded-full',
        'delivered': 'px-4 py-2 bg-green-100 text-green-800 rounded-full',
        'cancelled': 'px-4 py-2 bg-red-100 text-red-800 rounded-full'
    };
    return classes[status] || 'px-4 py-2 bg-gray-100 text-gray-800 rounded-full';
}

/**
 * Initialize tracking map
 */
function initTrackingMap() {
    const mapElement = document.getElementById('tracking-map');
    if (!mapElement) return;
    
    const lat = parseFloat(mapElement.dataset.latitude) || 23.8103;
    const lng = parseFloat(mapElement.dataset.longitude) || 90.4125;
    
    // Initialize Leaflet map (placeholder for future implementation)
    window.trackingMap = {
        center: { lat, lng },
        marker: null
    };
    
    console.log('Map initialized at:', lat, lng);
}

/**
 * Update map marker position
 */
function updateMapMarker(lat, lng) {
    if (window.trackingMap) {
        window.trackingMap.center = { lat, lng };
        console.log('Map updated to:', lat, lng);
        // In production, this would update actual map marker
    }
}

/**
 * Format date time
 */
function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Export tracking refresh for external use
window.refreshTracking = refreshTrackingStatus;
