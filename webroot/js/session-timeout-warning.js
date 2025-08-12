document.addEventListener('DOMContentLoaded', () => {
    const SESSION_DURATION_MINUTES = 300; // 5 hours
    const WARNING_BEFORE_EXPIRY_MINUTES = 5; // Warn 5 minutes before expiry
    const GRACE_PERIOD_MINUTES = 5; // 5 minute grace period

    const SESSION_DURATION_MS = SESSION_DURATION_MINUTES * 60 * 1000;
    const WARNING_TIME_MS = WARNING_BEFORE_EXPIRY_MINUTES * 60 * 1000; // Use minutes constant
    const GRACE_PERIOD_MS = GRACE_PERIOD_MINUTES * 60 * 1000; // Use minutes constant

    const LOGOUT_URL = '/users/logout';
    const KEEP_ALIVE_URL = '/users/keep-alive';

    let warningTimerId = null;
    let gracePeriodTimerId = null;

    function startWarningTimer() {
        // Clear any existing timer before starting a new one
        if (warningTimerId) {
            clearTimeout(warningTimerId);
        }
        const warningDelay = SESSION_DURATION_MS - WARNING_TIME_MS;
        console.log(`Session warning timer started. Will warn in ${warningDelay / 1000 / 60} minutes.`);
        warningTimerId = setTimeout(showWarningDialog, warningDelay);
    }

    function showWarningDialog() {
        console.log('Showing session expiry warning.');
        // Start the grace period timer immediately
        gracePeriodTimerId = setTimeout(() => {
            console.log('Grace period expired. Logging out.');
            redirectToLogout();
        }, GRACE_PERIOD_MS);

        const stayLoggedIn = confirm(
            'Your session is about to expire due to inactivity. Click OK to stay logged in.'
        );

        if (stayLoggedIn) {
            console.log('User chose to stay logged in.');
            // Clear the grace period timeout
            if (gracePeriodTimerId) {
                clearTimeout(gracePeriodTimerId);
                gracePeriodTimerId = null;
                console.log('Grace period timer cleared.');
            }
            // Ping the keep-alive endpoint
            fetch(KEEP_ALIVE_URL)
                .then(response => {
                    if (response.ok) {
                        console.log('Keep-alive request successful. Restarting session timer.');
                        // Restart the main warning timer
                        startWarningTimer();
                    } else {
                        console.error('Keep-alive request failed:', response.status, response.statusText);
                        // Optionally handle non-OK responses, maybe still restart timer or log out
                        // For now, we'll assume failure means we should probably log out just in case
                        redirectToLogout();
                    }
                })
                .catch(error => {
                    console.error('Error during keep-alive fetch:', error);
                    // Handle network errors etc. - maybe log out as a safe default
                    redirectToLogout();
                });
        } else {
            console.log('User chose to log out (cancelled dialog).');
            // Clear the grace period timer as we are logging out immediately
            if (gracePeriodTimerId) {
                clearTimeout(gracePeriodTimerId);
                gracePeriodTimerId = null;
            }
            redirectToLogout();
        }
    }

    function redirectToLogout() {
        console.log(`Redirecting to logout URL: ${LOGOUT_URL}`);
        window.location.href = LOGOUT_URL;
    }

    // Start the initial timer when the page loads
    startWarningTimer();
});