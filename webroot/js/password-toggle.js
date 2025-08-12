/**
 * Standardized Password Visibility Toggle Functionality
 */
document.addEventListener('DOMContentLoaded', () => {
    const passwordToggleButtons = document.querySelectorAll('.password-toggle-btn');

    passwordToggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            const container = button.closest('.password-input-container'); // Find the container
            if (!container) return; // Exit if container not found

            const passwordInput = container.querySelector('input[type="password"], input[type="text"]'); // Find input within container
            const icon = button.querySelector('i');

            if (!passwordInput || !icon) return; // Exit if input or icon not found

            // Toggle input type
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            // Toggle icon class
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);

            // Update ARIA attributes
            button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            button.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
        });
    });
});