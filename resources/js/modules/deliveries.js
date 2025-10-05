/**
 * =========================================
 * DELIVERIES MODULE
 * =========================================
 * Delivery management and operations
 */

export function initDeliveries() {
    console.log('📦 Deliveries module initialized');
    
    // Initialize delivery assignment
    initDeliveryAssignment();
    
    // Initialize status updates
    initStatusUpdates();
    
    // Initialize bulk operations
    initBulkOperations();
}

/**
 * Initialize delivery assignment to drones
 */
function initDeliveryAssignment() {
    const assignButtons = document.querySelectorAll('[data-assign-delivery]');
    
    assignButtons.forEach(button => {
        button.addEventListener('click', function() {
            const deliveryId = this.dataset.assignDelivery;
            showDroneSelectionModal(deliveryId);
        });
    });
}

/**
 * Show drone selection modal
 */
function showDroneSelectionModal(deliveryId) {
    axios.get('/api/drones/available')
        .then(response => {
            const drones = response.data;
            displayDroneModal(deliveryId, drones);
        })
        .catch(error => {
            window.showToast('Failed to load available drones', 'error');
        });
}

/**
 * Display drone selection modal
 */
function displayDroneModal(deliveryId, drones) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4';
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-drone mr-2 text-green-600"></i>Select Drone
                    </h3>
                    <button class="close-modal text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                ${drones.length === 0 ? 
                    '<p class="text-center text-gray-500 py-8">No available drones</p>' :
                    `<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${drones.map(drone => `
                            <div class="border rounded-lg p-4 hover:border-green-500 cursor-pointer transition drone-card"
                                 data-drone-id="${drone.id}">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">${drone.name}</h4>
                                        <p class="text-sm text-gray-500">${drone.model}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        ${drone.status}
                                    </span>
                                </div>
                                
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex justify-between">
                                        <span>Battery:</span>
                                        <span class="font-medium">${drone.battery_level}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" 
                                             style="width: ${drone.battery_level}%"></div>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Max Payload:</span>
                                        <span class="font-medium">${drone.max_payload_kg} kg</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Max Range:</span>
                                        <span class="font-medium">${drone.max_range_km} km</span>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>`
                }
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle drone selection
    modal.querySelectorAll('.drone-card').forEach(card => {
        card.addEventListener('click', function() {
            const droneId = this.dataset.droneId;
            assignDroneToDelivery(deliveryId, droneId);
            modal.remove();
        });
    });
    
    // Close modal
    modal.querySelector('.close-modal').addEventListener('click', () => modal.remove());
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
    });
}

/**
 * Assign drone to delivery
 */
function assignDroneToDelivery(deliveryId, droneId) {
    window.showLoading('Assigning drone...');
    
    axios.post(`/api/deliveries/${deliveryId}/assign`, { drone_id: droneId })
        .then(response => {
            window.hideLoading();
            window.showToast('Drone assigned successfully', 'success');
            setTimeout(() => window.location.reload(), 1000);
        })
        .catch(error => {
            window.hideLoading();
            window.showToast(error.response?.data?.message || 'Assignment failed', 'error');
        });
}

/**
 * Initialize status updates
 */
function initStatusUpdates() {
    const statusButtons = document.querySelectorAll('[data-update-status]');
    
    statusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const deliveryId = this.dataset.deliveryId;
            const newStatus = this.dataset.updateStatus;
            updateDeliveryStatus(deliveryId, newStatus);
        });
    });
}

/**
 * Update delivery status
 */
function updateDeliveryStatus(deliveryId, status) {
    axios.patch(`/api/deliveries/${deliveryId}/status`, { status })
        .then(response => {
            window.showToast(`Status updated to ${status}`, 'success');
            setTimeout(() => window.location.reload(), 1000);
        })
        .catch(error => {
            window.showToast('Failed to update status', 'error');
        });
}

/**
 * Initialize bulk operations
 */
function initBulkOperations() {
    const selectAllCheckbox = document.getElementById('select-all-deliveries');
    const itemCheckboxes = document.querySelectorAll('.delivery-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActionsVisibility();
        });
    }
    
    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActionsVisibility);
    });
    
    function updateBulkActionsVisibility() {
        const checkedCount = document.querySelectorAll('.delivery-checkbox:checked').length;
        if (bulkActions) {
            bulkActions.classList.toggle('hidden', checkedCount === 0);
            const countElement = bulkActions.querySelector('[data-selected-count]');
            if (countElement) {
                countElement.textContent = checkedCount;
            }
        }
    }
}

// Export for global use
window.assignDroneToDelivery = assignDroneToDelivery;
window.updateDeliveryStatus = updateDeliveryStatus;
