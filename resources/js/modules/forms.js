/**
 * =========================================
 * FORMS MODULE
 * =========================================
 * Enhanced form handling with validation and AJAX
 */

/**
 * Initialize form validations
 */
export function initFormValidations() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                window.showToast('Please fix the errors in the form', 'error');
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

/**
 * Validate entire form
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('[required], [data-validate]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Validate single field
 */
function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let errorMessage = '';
    
    // Remove previous error
    clearFieldError(field);
    
    // Required validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    
    // Email validation
    else if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }
    
    // Min length validation
    else if (field.hasAttribute('minlength')) {
        const minLength = parseInt(field.getAttribute('minlength'));
        if (value.length < minLength) {
            isValid = false;
            errorMessage = `Minimum ${minLength} characters required`;
        }
    }
    
    // Max length validation
    else if (field.hasAttribute('maxlength')) {
        const maxLength = parseInt(field.getAttribute('maxlength'));
        if (value.length > maxLength) {
            isValid = false;
            errorMessage = `Maximum ${maxLength} characters allowed`;
        }
    }
    
    // Number validation
    else if (type === 'number' && value) {
        if (field.hasAttribute('min') && parseFloat(value) < parseFloat(field.getAttribute('min'))) {
            isValid = false;
            errorMessage = `Minimum value is ${field.getAttribute('min')}`;
        }
        if (field.hasAttribute('max') && parseFloat(value) > parseFloat(field.getAttribute('max'))) {
            isValid = false;
            errorMessage = `Maximum value is ${field.getAttribute('max')}`;
        }
    }
    
    // Phone validation
    else if (field.dataset.validate === 'phone' && value) {
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        if (!phoneRegex.test(value) || value.length < 10) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        }
    }
    
    // Display error if invalid
    if (!isValid) {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

/**
 * Show field error
 */
function showFieldError(field, message) {
    field.classList.add('border-red-500');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-red-500 text-sm mt-1';
    errorDiv.textContent = message;
    
    field.parentElement.appendChild(errorDiv);
}

/**
 * Clear field error
 */
function clearFieldError(field) {
    field.classList.remove('border-red-500');
    
    const existingError = field.parentElement.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

/**
 * Initialize file upload with progress
 */
export function initFileUploads() {
    const fileInputs = document.querySelectorAll('input[type="file"][data-upload-progress]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                showUploadProgress(this, file);
            }
        });
    });
}

/**
 * Show upload progress
 */
function showUploadProgress(input, file) {
    const progressContainer = document.createElement('div');
    progressContainer.className = 'mt-2';
    progressContainer.innerHTML = `
        <div class="flex items-center justify-between text-sm mb-1">
            <span class="text-gray-700">${file.name}</span>
            <span class="text-gray-500"><span class="upload-percentage">0</span>%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="upload-bar bg-blue-600 h-2 rounded-full transition-all" style="width: 0%"></div>
        </div>
    `;
    
    input.parentElement.appendChild(progressContainer);
    
    // Simulate upload progress (in production, use actual upload)
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress > 100) {
            progress = 100;
            clearInterval(interval);
        }
        
        progressContainer.querySelector('.upload-bar').style.width = progress + '%';
        progressContainer.querySelector('.upload-percentage').textContent = Math.round(progress);
    }, 200);
}

// Export for global use
window.validateForm = validateForm;
window.validateField = validateField;
