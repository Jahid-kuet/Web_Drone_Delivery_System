/**
 * =========================================
 * DRONE DELIVERY SYSTEM - MAIN APP.JS
 * =========================================
 * Enhanced with real-time features, AJAX operations,
 * notifications, and dynamic interactions
 */

import './bootstrap';

// Import modules
import { initNotifications, showToast } from './modules/notifications';

/**
 * =========================================
 * GLOBAL APP INITIALIZATION
 * =========================================
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('[DRONE] Drone Delivery System - Frontend Initialized');

    // Initialize core features
    initNotifications();
    initDeleteConfirmation();
    initImagePreviews();
    initAutoSave();
    initSearchAutocomplete();
    initAjaxForms();
    initDataTableFilters();
    initTooltips();
    
    // Initialize page-specific features
    initPageSpecificModules();
});

/**
 * =========================================
 * PAGE-SPECIFIC MODULE INITIALIZATION
 * =========================================
 */
function initPageSpecificModules() {
    const currentPath = window.location.pathname;
    
    // Dashboard page
    if (currentPath.includes('/dashboard')) {
        import('./modules/dashboard').then(module => {
            module.initDashboard();
        });
    }
    
    // Tracking pages
    if (currentPath.includes('/track') || currentPath.includes('/deliveries')) {
        import('./modules/tracking').then(module => {
            module.initTracking();
        });
        import('./modules/deliveries').then(module => {
            module.initDeliveries();
        });
    }
    
    // Forms with validation
    if (document.querySelector('form[data-validate]')) {
        import('./modules/forms').then(module => {
            module.initFormValidations();
            module.initFileUploads();
        });
    }
}

/**
 * =========================================
 * DELETE CONFIRMATION
 * =========================================
 */
function initDeleteConfirmation() {
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const confirmed = confirm('Are you sure you want to delete this item? This action cannot be undone.');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
}

/**
 * =========================================
 * IMAGE PREVIEW
 * =========================================
 */
function initImagePreviews() {
    document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    let preview = document.querySelector(`#${input.id}-preview`);
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = `${input.id}-preview`;
                        preview.className = 'mt-2 max-w-xs rounded-lg shadow';
                        input.parentElement.appendChild(preview);
                    }
                    preview.src = event.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    });
}

/**
 * =========================================
 * AUTO-SAVE FUNCTIONALITY
 * =========================================
 */
function initAutoSave() {
    const autoSaveForms = document.querySelectorAll('[data-autosave]');
    
    autoSaveForms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('change', debounce(function() {
                const formData = new FormData(form);
                
                axios.post(form.dataset.autosaveUrl || form.action, formData)
                    .then(response => {
                        showNotification('Draft saved', 'success');
                    })
                    .catch(error => {
                        console.error('Autosave failed:', error);
                    });
            }, 1000));
        });
    });
}

/**
 * =========================================
 * SEARCH AUTOCOMPLETE
 * =========================================
 */
function initSearchAutocomplete() {
    const searchInputs = document.querySelectorAll('[data-autocomplete]');
    
    searchInputs.forEach(input => {
        const resultsContainer = document.createElement('div');
        resultsContainer.className = 'absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden';
        input.parentElement.classList.add('relative');
        input.parentElement.appendChild(resultsContainer);
        
        input.addEventListener('input', debounce(function() {
            const query = input.value.trim();
            
            if (query.length < 2) {
                resultsContainer.classList.add('hidden');
                return;
            }
            
            axios.get(input.dataset.autocompleteUrl, {
                params: { q: query }
            })
            .then(response => {
                displayAutocompleteResults(response.data, resultsContainer, input);
            })
            .catch(error => {
                console.error('Autocomplete failed:', error);
            });
        }, 300));
        
        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.add('hidden');
            }
        });
    });
}

function displayAutocompleteResults(results, container, input) {
    if (results.length === 0) {
        container.innerHTML = '<div class="p-3 text-gray-500 text-sm">No results found</div>';
        container.classList.remove('hidden');
        return;
    }
    
    container.innerHTML = results.map(item => `
        <div class="p-3 hover:bg-gray-100 cursor-pointer border-b last:border-b-0" data-value="${item.id}">
            <div class="font-medium text-gray-900">${item.name}</div>
            ${item.description ? `<div class="text-sm text-gray-500">${item.description}</div>` : ''}
        </div>
    `).join('');
    
    container.querySelectorAll('[data-value]').forEach(element => {
        element.addEventListener('click', function() {
            input.value = element.querySelector('.font-medium').textContent;
            input.dataset.selectedId = element.dataset.value;
            container.classList.add('hidden');
        });
    });
    
    container.classList.remove('hidden');
}

/**
 * =========================================
 * AJAX FORMS
 * =========================================
 */
function initAjaxForms() {
    document.querySelectorAll('[data-ajax-form]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            
            axios.post(form.action, formData)
                .then(response => {
                    showNotification(response.data.message || 'Success!', 'success');
                    
                    // Redirect if specified
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        form.reset();
                    }
                })
                .catch(error => {
                    const message = error.response?.data?.message || 'An error occurred';
                    showNotification(message, 'error');
                    
                    // Display validation errors
                    if (error.response?.data?.errors) {
                        displayValidationErrors(form, error.response.data.errors);
                    }
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });
    });
}

function displayValidationErrors(form, errors) {
    // Clear previous errors
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    
    // Display new errors
    Object.keys(errors).forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-red-500 text-sm mt-1';
            errorDiv.textContent = errors[field][0];
            input.parentElement.appendChild(errorDiv);
            input.classList.add('border-red-500');
        }
    });
}

/**
 * =========================================
 * DATA TABLE FILTERS
 * =========================================
 */
function initDataTableFilters() {
    const filterForms = document.querySelectorAll('[data-table-filter]');
    
    filterForms.forEach(form => {
        const inputs = form.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            input.addEventListener('change', debounce(function() {
                form.submit();
            }, 500));
        });
    });
}

/**
 * =========================================
 * TOOLTIPS
 * =========================================
 */
function initTooltips() {
    document.querySelectorAll('[data-tooltip]').forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-3 py-2 text-sm text-white bg-gray-900 rounded shadow-lg';
            tooltip.textContent = element.dataset.tooltip;
            tooltip.id = 'tooltip-' + Math.random();
            
            document.body.appendChild(tooltip);
            
            const rect = element.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
            tooltip.style.left = (rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)) + 'px';
            
            element.addEventListener('mouseleave', function() {
                tooltip.remove();
            }, { once: true });
        });
    });
}

/**
 * =========================================
 * UTILITY FUNCTIONS
 * =========================================
 */

// Debounce function
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

// Show notification
function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-xl text-white transition-all transform ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' :
        'bg-blue-500'
    }`;
    
    toast.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' :
                'fa-info-circle'
            }"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => toast.classList.add('opacity-100'), 10);
    
    // Animate out and remove
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Export for global access
window.showNotification = showNotification;
window.debounce = debounce;
