/**
 * Jenbury Financial - Custom JavaScript
 */

// File uploader class
class FileUploader {
    constructor(inputElement, options = {}) {
        this.input = inputElement;
        this.options = {
            chunkSize: 1024 * 1024, // 1MB chunks
            maxFileSize: 5 * 1024 * 1024, // 5MB max
            allowedTypes: [], // Initialize empty, will be populated from 'accept' attribute
            maxWidth: 2048,
            maxHeight: 2048,
            ...options
        };
        this.inputName = this.input.name; // Store the input's name attribute

        this.uploadId = null;
        this.chunks = [];
        this.currentChunk = 0;
        this.totalChunks = 0;

        this.parseAcceptAttribute(); // Parse the accept attribute first
        this.setupListeners();
    }

    parseAcceptAttribute() {
        const acceptAttr = this.input.getAttribute('accept');
        if (acceptAttr) {
            this.options.allowedTypes = acceptAttr.split(',')
                                                .map(type => type.trim())
                                                .filter(type => type); // Remove empty strings
        } else {
            // Fallback if no accept attribute is defined (though it should be)
            this.options.allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            // console.warn('[FileUploader] Input element has no "accept" attribute. Falling back to default image types.');
        }
    }

    setupListeners() {
        this.input.addEventListener('change', (e) => this.handleFileSelect(e));
    }

    async handleFileSelect(e) {
        const file = e.target.files[0];
        if (!file) return;

        try {
            await this.validateFile(file);
            this.uploadId = this.generateUploadId();
            this.prepareChunks(file);
            this.updateProgress(0);
            this.uploadNextChunk();
        } catch (error) {
            this.showError(error.message);
        }
    }

    async validateFile(file) {
        // Updated validation logic to handle parsed 'accept' types and wildcards
        const isAllowed = this.options.allowedTypes.some(allowedType => {
            if (allowedType.endsWith('/*')) {
                // Handle wildcard (e.g., "image/*")
                return file.type.startsWith(allowedType.slice(0, -2));
            } else {
                // Handle specific MIME type (e.g., "application/pdf")
                return file.type === allowedType;
            }
        });

        if (!isAllowed) {
             // Make the error message more dynamic based on the actual allowed types
             const allowedTypesString = this.options.allowedTypes.join(', ');
             throw new Error(`Invalid file type. Allowed types are: ${allowedTypesString}.`);
        }

        if (file.size > this.options.maxFileSize) {
            throw new Error('File size exceeds maximum allowed size (5MB).');
        }

        // Check image dimensions
        const dimensions = await this.getImageDimensions(file);
        if (dimensions.width > this.options.maxWidth || dimensions.height > this.options.maxHeight) {
            throw new Error(`Image dimensions exceed maximum allowed (${this.options.maxWidth}x${this.options.maxHeight}).`);
        }

        // Preview image
        this.previewFile(file);
    }

    getImageDimensions(file) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve({ width: img.width, height: img.height });
            img.onerror = () => reject(new Error('Failed to load image'));
            img.src = URL.createObjectURL(file);
        });
    }

    previewFile(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            $('.file-preview').html('<img src="' + e.target.result + '" class="img-preview">');
        };
        reader.readAsDataURL(file);
    }

    prepareChunks(file) {
        const chunkSize = this.options.chunkSize;
        this.totalChunks = Math.ceil(file.size / chunkSize);
        this.currentChunk = 0;
        this.file = file;
    }

    async uploadNextChunk() {
        if (this.currentChunk >= this.totalChunks) return;

        const chunk = this.getChunk();
        const formData = new FormData();
        formData.append(this.inputName, chunk); // Use the stored input name
        formData.append('upload_id', this.uploadId);
        formData.append('chunk', this.currentChunk);
        formData.append('chunks', this.totalChunks);

        // Find the form and add the 'type' field value
        const form = this.input.closest('form');
        if (form) {
            const typeInput = form.querySelector('[name="type"]:checked') || form.querySelector('[name="type"]');
            if (typeInput) {
                formData.append('type', typeInput.value);
            } else {
                 // console.warn('[FileUploader] Could not find type input in the form.');
            }
        } else {
             // console.warn('[FileUploader] Could not find parent form.');
        }
        try {
            const csrfToken = this.getCsrfToken(); // Get the token
            if (!csrfToken) {
                 throw new Error('CSRF token not found. Cannot upload.');
            }

            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: { // Add headers
                    'X-CSRF-Token': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest' // Add standard AJAX header
                },
                body: formData
            });

            const result = await response.json();

            if (result.complete) {
                this.updateProgress(100);
                this.showSuccess('Upload complete');

                // Update hidden input with the new image path
                $('input[name="image"]').val(result.path);
            } else {
                this.currentChunk++;
                const progress = (this.currentChunk / this.totalChunks) * 100;
                this.updateProgress(progress);
                this.uploadNextChunk();
            }
        } catch (error) {
            // Use the actual error message from the exception (e.g., JSON parse error)
            this.showError('Upload failed: ' + (error && error.message ? error.message : 'Unknown error'));
        }
    }

    getChunk() {
        const start = this.currentChunk * this.options.chunkSize;
        const end = Math.min(start + this.options.chunkSize, this.file.size);
        return this.file.slice(start, end);
    }

    generateUploadId() {
        return Array.from(crypto.getRandomValues(new Uint8Array(16)))
            .map(b => b.toString(16).padStart(2, '0'))
            .join('');
    }

    // Helper to get CSRF token from meta tag
    getCsrfToken() {
        const tokenElement = document.querySelector('meta[name="csrfToken"]');
        if (tokenElement) {
            return tokenElement.getAttribute('content');
        }
        // console.error('[FileUploader] CSRF token meta tag not found.'); // Keep for debugging if needed
        return null; // Token not found
    }

    updateProgress(percent) {
        const progressBar = $('.upload-progress');
        if (!progressBar.length) {
            $('.file-preview').after('<div class="upload-progress"><div class="progress-bar"></div></div>');
        }
        $('.progress-bar').css('width', percent + '%');
    }

    showError(message) {
        // Find the container and the standard error message element
        const inputContainer = $(this.input).closest('.input');
        let messageElement = inputContainer.find('.error-message');

        // If no standard error message element exists, create it
        if (!messageElement.length) {
             messageElement = $('<div class="error-message"></div>');
             inputContainer.append(messageElement); // Append inside the container
        }

        // Display the error
        messageElement.text(message).css({ display: 'block', color: 'var(--color-danger)' });
        $(this.input).addClass('is-invalid'); // Add invalid class to input
        inputContainer.addClass('is-invalid'); // Add invalid class to container
    }

    showSuccess(message) {
        $('.upload-error').remove();
        $('.file-preview').after(`<div class="upload-success text-success">${message}</div>`);
    }
}

// Function to adjust password toggle button position
function adjustPasswordTogglePosition() {
    const containers = document.querySelectorAll('.password-input-container');

    containers.forEach(container => {
        const input = container.querySelector('input.password-input');
        const button = container.querySelector('.password-toggle-btn');

        if (input && button) {
            // Calculate the vertical center of the input relative to the container
            const inputTopRelativeToContainer = input.offsetTop;
            const inputHeight = input.offsetHeight;
            const inputCenter = inputTopRelativeToContainer + (inputHeight / 2);

            // Calculate the button's half-height
            const buttonHeight = button.offsetHeight;
            const buttonHalfHeight = buttonHeight / 2;

            // Calculate the desired top position for the button
            // to align its center with the input's center
            const buttonTop = inputCenter - buttonHalfHeight;

            // Apply the calculated top style
            // Use inline style for highest specificity
            button.style.top = `${buttonTop}px`;
            // Ensure transform is cleared if it was somehow re-added
            button.style.transform = 'none';
        }
    });
}

$(document).ready(function() {
    // Initialize dropdown menus
    $('.dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        $(this).siblings('.dropdown-menu').toggleClass('show');
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });

    // Flash message auto-dismiss
    setTimeout(function() {
        $('.message').fadeOut('slow');
    }, 5000);

    // Removed conflicting form validation on submit (handled by form-validation.js)

    // Course and module purchase confirmation
    $('.purchase-button').on('click', function(e) {
        if (!confirm('Are you sure you want to purchase this item?')) {
            e.preventDefault();
        }
    });

    // Admin content editor
    if ($('#content-editor').length) {
        // Simple rich text editor functionality
        $('.editor-toolbar button').on('click', function(e) {
            e.preventDefault();
            const command = $(this).data('command');
            const value = $(this).data('value') || null;

            if (command === 'insertHTML') {
                const html = prompt('Enter HTML:');
                if (html) {
                    document.execCommand(command, false, html);
                }
            } else {
                document.execCommand(command, false, value);
            }
        });
    }

    // Initialize file uploader for all file inputs within validated forms
    $('form.needs-validation input[type="file"]').each(function() {
        // We might want to pass specific options based on the input later
        // e.g., different allowedTypes or maxFileSize for general files vs images
        // For now, use default options (5MB limit, image types) which might need adjustment for non-image uploads
        new FileUploader(this);
    });

    // Responsive navigation toggle
    $('.nav-toggle').on('click', function() {
        $('.main-nav').toggleClass('show');
    });

    // --- NEW: Adjust password toggle positions ---
    adjustPasswordTogglePosition();
    // --- END NEW ---

    // Removed "Mark as Complete" AJAX handling as it's now a direct link.
});

// Adjust positions on window resize
let resizeTimer;
$(window).on('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        adjustPasswordTogglePosition();
    }, 250); // Debounce resize event
});