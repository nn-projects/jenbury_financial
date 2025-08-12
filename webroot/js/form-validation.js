/**
 * Jenbury Financial - Real-time Form Validation
 */
document.addEventListener('DOMContentLoaded', () => {
    // Select all forms that should have real-time validation
    // We might need to refine this selector or add specific classes/IDs to target forms
    const formsToValidate = document.querySelectorAll('form.needs-validation'); // Example selector

    formsToValidate.forEach(form => {
        // Prevent default submission if validation fails
        form.addEventListener('submit', event => {
            if (!validateForm(form)) {
                event.preventDefault();
                event.stopPropagation();
                // Optionally, focus the first invalid field
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
            // Add a class to show validation has been attempted
            form.classList.add('was-validated');
        });

        // Add real-time validation listeners to relevant input fields
        const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="file"], textarea, select, input[type="checkbox"]'); // Added file input
        inputs.forEach(input => {
            // Validate on blur and input/change events
            const eventType = (input.type === 'checkbox' || input.type === 'file' || input.tagName === 'SELECT') ? 'change' : 'input'; // Use 'change' for checkbox, file, select
            input.addEventListener('blur', () => {
                validateField(input);
            });
            input.addEventListener(eventType, () => {
                 validateField(input);
            });

            // Special handling for password confirmation
            if (input.matches('[name*="confirm_password"]')) {
                const passwordInput = form.querySelector('input[name="password"]');
                if (passwordInput) {
                    passwordInput.addEventListener('input', () => validateField(input)); // Re-validate confirm when original changes
                }
            }
        });
    });
});

/**
 * Validates all fields within a given form.
 * @param {HTMLFormElement} form - The form element to validate.
 * @returns {boolean} - True if the form is valid, false otherwise.
 */
function validateForm(form) {
    let isFormValid = true;
    const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="number"], textarea, select');
    inputs.forEach(input => {
        if (!validateField(input)) {
            isFormValid = false;
        }
    });
    return isFormValid;
}

/**
 * Validates a single input field based on its attributes.
 * @param {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} input - The input field to validate.
 * @returns {boolean} - True if the field is valid, false otherwise.
 */
function validateField(input) {
    let isValid = true;
    let message = '';
    const value = input.type === 'checkbox' ? input.checked : input.value.trim(); // Handle checkbox value
    const inputContainer = input.closest('.input') || input.parentElement; // Find a suitable container
    // Look for the standard CakePHP error message element OR our previously added one as a fallback
    let messageElement = inputContainer ? inputContainer.querySelector('.error-message') : null;
    // If default .error-message doesn't exist yet (no server error), try finding our placeholder
    if (!messageElement && inputContainer) {
         messageElement = inputContainer.querySelector('.validation-message'); // This fallback is likely no longer needed
    }

    // --- Validation Rules ---

    // 1. Required (Text, Email, Password, etc.)
    if (input.type !== 'checkbox' && input.hasAttribute('required') && value === '') {
        isValid = false;
        message = 'This field is required.';
    }

    // 2. Email Format (only if not empty and required check passed or not required)
    if (input.hasAttribute('required') && value === '') {
        isValid = false;
        message = 'This field is required.';
    }

    // 2. Email Format (only if not empty and required check passed or not required)
    if (isValid && input.type === 'email' && value !== '') {
        // Basic email regex (consider a more robust one if needed)
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            message = 'Please enter a valid email address.';
        }
    }

    // 3. Password Complexity & Length (only for password fields, if not empty and previous checks passed)
    if (isValid && input.type === 'password' && value !== '' && input.name !== 'current_password') { // Check complexity for new passwords only
        const minLength = input.hasAttribute('minlength') ? parseInt(input.getAttribute('minlength'), 10) : 8; // Default 8 if not set

        if (value.length < minLength) {
            isValid = false;
            message = `Must be at least ${minLength} characters long.`;
        } else if (!/[a-z]/.test(value)) { // Check for lowercase
            isValid = false;
            message = 'Must include at least one lowercase letter.';
        } else if (!/[A-Z]/.test(value)) { // Check for uppercase
            isValid = false;
            message = 'Must include at least one uppercase letter.';
        } else if (!/\d/.test(value)) { // Check for number
             isValid = false;
             message = 'Must include at least one number.';
        }
        // Note: The UsersTable validation also checks for a special character,
        // but the help text on register/change password doesn't mention it consistently.
        // Adding it here for consistency with backend:
        else if (!/[\W_]/.test(value)) { // Check for special character (non-alphanumeric)
             isValid = false;
             message = 'Must include at least one special character.';
        }
    }

    // 4. Maximum Length (only if not empty and previous checks passed) - Apply generally
     if (isValid && input.hasAttribute('maxlength') && value !== '') {
        const maxLength = parseInt(input.getAttribute('maxlength'), 10);
        if (value.length > maxLength) {
            // Note: Maxlength attribute often prevents exceeding, but good to double-check
            isValid = false;
            // message = `Cannot exceed ${maxLength} characters.`;
        }
    }

    // 5. Pattern Match (if attribute exists, not empty, and previous checks passed)
    if (isValid && input.hasAttribute('pattern') && value !== '') {
        const pattern = new RegExp(input.getAttribute('pattern'));
        if (!pattern.test(value)) {
            isValid = false;
            // Use the input's title attribute for the error message if available, otherwise generic message
            message = input.getAttribute('title') || 'Please match the requested format.';
        }
    }

    // 5. Password Confirmation (only if not empty and previous checks passed)
    if (isValid && input.matches('[name*="confirm_password"]') && value !== '') {
        const form = input.closest('form');
        const passwordInput = form ? form.querySelector('input[name="password"]') : null;
        if (passwordInput && passwordInput.value !== value) {
            isValid = false;
            message = 'Passwords do not match.';
        }
    }

    // 6. Numeric Input Validation (type="number")
    if (isValid && input.type === 'number' && value !== '') {
        // Check if it's actually a number and non-negative
        // Allows integers and decimals, rejects negatives, letters, symbols.
        if (isNaN(value) || !/^\d*\.?\d+$/.test(value) || parseFloat(value) < 0) {
             isValid = false;
             message = 'Please enter a valid non-negative number.';
        } else {
            const numValue = parseFloat(value);
            // Check min value
            if (input.hasAttribute('min')) {
                const min = parseFloat(input.getAttribute('min'));
                if (numValue < min) {
                    isValid = false;
                    message = `Value must be ${min} or greater.`;
                }
            }
            // Check max value (if attribute exists)
            if (isValid && input.hasAttribute('max')) {
                const max = parseFloat(input.getAttribute('max'));
                if (numValue > max) {
                    isValid = false;
                    message = `Value must be ${max} or less.`;
                }
            }
        }
    }

    // 7. Required Checkbox
    if (isValid && input.type === 'checkbox' && input.hasAttribute('required') && !input.checked) {
        isValid = false;
        message = 'You must agree to the terms.'; // Or a more generic message
    }

    // 8. File Input Validation (type="file")
    if (isValid && input.type === 'file' && input.files && input.files.length > 0) {
        const file = input.files[0];
        const acceptAttr = input.getAttribute('accept');

        if (acceptAttr) {
            const allowedTypes = acceptAttr.split(',').map(type => type.trim().toLowerCase());
            let fileTypeValid = false;

            // Check against MIME types and extensions
            const fileExtension = file.name.includes('.') ? '.' + file.name.split('.').pop().toLowerCase() : ''; // Handle names without extension
            const fileMimeType = file.type ? file.type.toLowerCase() : ''; // Handle missing MIME type

            for (const allowedType of allowedTypes) {
                if (allowedType.startsWith('.')) { // Check extension
                    if (fileExtension === allowedType) {
                        fileTypeValid = true;
                        break;
                    }
                } else if (allowedType.includes('/')) { // Check MIME type (e.g., "application/pdf", "image/*")
                    if (allowedType.endsWith('/*')) { // Wildcard MIME type (e.g., "image/*")
                        const baseMime = allowedType.replace('/*', '');
                        if (fileMimeType.startsWith(baseMime + '/')) {
                            fileTypeValid = true;
                            break;
                        }
                    } else { // Specific MIME type (e.g., "application/pdf")
                        if (fileMimeType === allowedType) {
                            fileTypeValid = true;
                            break;
                        }
                    }
                }
            }

            if (!fileTypeValid) {
                isValid = false;
                message = `Invalid file type. Please select a file with one of the following types: ${acceptAttr}`;
            }
        }
        // Add file size validation if needed (example)
        // const maxSize = 5 * 1024 * 1024; // 5MB example
        // if (isValid && file.size > maxSize) {
        //     isValid = false;
        //     message = `File size cannot exceed ${maxSize / 1024 / 1024}MB.`;
        // }
    }


    // --- Update UI ---
    updateValidationUI(input, inputContainer, messageElement, isValid, message);

    return isValid;
}

/**
 * Updates the UI elements (input class, message display) based on validation result.
 * @param {HTMLElement} input - The input element.
 * @param {HTMLElement} inputContainer - The container div for the input.
 * @param {HTMLElement} messageElement - The element to display the validation message.
 * @param {boolean} isValid - Whether the input is valid.
 * @param {string} message - The validation message to display.
 */
function updateValidationUI(input, inputContainer, messageElement, isValid, message) {
    // Only check for container initially. We will handle null messageElement later if needed.
    if (!inputContainer) {
        // console.warn('[Validation] Could not find container for input:', input); // DEBUG - Removed
        return;
    }

    // Remove previous states from input and container
    input.classList.remove('is-valid', 'is-invalid');
    inputContainer.classList.remove('is-valid', 'is-invalid');

    // Clear existing message element if it exists
    if (messageElement) {
        messageElement.textContent = '';
        messageElement.style.display = 'none';
    }

    if (input.type !== 'checkbox' && input.value.trim() === '' && !input.hasAttribute('required')) {
         // Don't show validation state for empty, non-required fields (ignore checkboxes here)
         // console.log('[Validation] Skipping UI update for empty non-required field:', input.name || input.id); // DEBUG - Removed
         return;
    }

    if (isValid) {
        input.classList.add('is-valid');
        inputContainer.classList.add('is-valid');
        // If a message element exists (from previous error or server-side), ensure it's hidden by removing text
        if (messageElement) {
             messageElement.textContent = '';
             // messageElement.style.display = 'none'; // Rely on CSS class via .is-valid/.is-invalid on container
        }
        // Optionally show a success message or icon here if needed
    } else {
        input.classList.add('is-invalid');
        inputContainer.classList.add('is-invalid');

        // If the message element doesn't exist, create it NOW
        if (!messageElement) {
            // console.log('[Validation] Creating message element for:', input.name || input.id); // DEBUG - Removed
            messageElement = document.createElement('div');
            messageElement.className = 'error-message'; // Use the standard CakePHP class
            // Append after the input element itself, or at the end of the container
            // Check if the input is a checkbox, place after label if so
            if (input.type === 'checkbox' && input.nextElementSibling?.tagName === 'LABEL') {
                 inputContainer.insertBefore(messageElement, input.nextElementSibling.nextSibling);
            } else if (input.nextSibling) {
                 inputContainer.insertBefore(messageElement, input.nextSibling);
            } else {
                 inputContainer.appendChild(messageElement);
            }
        }

        messageElement.textContent = message;
        // messageElement.style.display = 'block'; // Rely on CSS class via .is-invalid on container
        // messageElement.style.color = 'var(--color-danger)'; // Color should be handled by CSS rule
    }
}