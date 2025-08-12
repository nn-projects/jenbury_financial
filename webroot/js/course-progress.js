console.log('course-progress.js: Script start.'); // <-- ADD LOG 1

document.addEventListener('DOMContentLoaded', function() {
    console.log('course-progress.js: DOMContentLoaded fired.'); // <-- ADD LOG 2
    // Select all module action buttons (both in the list and navigation)
    const moduleActionButtons = document.querySelectorAll('.module-action-button');
    const progressBar = document.getElementById('course-progress-bar');
    const progressText = document.getElementById('course-progress-text');

    // Function to get CSRF token from meta tag or hidden input
    function getCsrfToken() {
        const tokenInput = document.querySelector('input[name="_csrfToken"]');
        if (tokenInput) {
            // console.log('CSRF Token found in input.'); // DEBUG
            return tokenInput.value;
        }
        const tokenMeta = document.querySelector('meta[name="csrfToken"]');
        if (tokenMeta) {
            // console.log('CSRF Token found in meta tag.'); // DEBUG
            return tokenMeta.getAttribute('content');
        }
        console.error('CSRF token not found.');
        return null;
    }

    // Function to update progress via AJAX (for starting modules)
    async function updateProgress(moduleId, newStatus) {
        console.log(`updateProgress called for module ${moduleId}, status ${newStatus}`); // DEBUG
        const csrfToken = getCsrfToken();
        if (!csrfToken || !moduleId || !newStatus) {
            console.error('Missing data for progress update:', { moduleId, newStatus, csrfToken });
            return;
        }

        if (newStatus === 'completed') {
             const relatedButton = document.querySelector(`.module-action-button[data-module-id="${moduleId}"]`);
             if (relatedButton && relatedButton.dataset.status === 'completed') {
                 console.log(`Module ${moduleId} already completed. No update sent.`);
                 return;
             }
        }

        try {
            console.log(`Sending AJAX request to /progress/update for module ${moduleId}`); // DEBUG
            const response = await fetch('/progress/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    module_id: moduleId,
                    status: newStatus
                })
            });

            console.log(`AJAX response status for /progress/update: ${response.status}`); // DEBUG
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('AJAX response data for /progress/update:', result); // DEBUG

            if (result.success && progressBar && progressText) {
                console.log('Progress updated successfully. New percentage:', result.newPercentage);
                progressBar.style.width = result.newPercentage + '%';
                progressText.textContent = result.newPercentage + '% Complete';

                const buttonsToUpdate = document.querySelectorAll(`.module-action-button[data-module-id="${moduleId}"]`);
                buttonsToUpdate.forEach(button => {
                    if (newStatus === 'in_progress') {
                        button.textContent = 'Continue Learning';
                        button.classList.remove('button-completed');
                        button.disabled = false;
                        button.dataset.status = 'in_progress';
                    } else if (newStatus === 'completed') {
                        button.textContent = 'Completed';
                        button.classList.add('button-completed');
                        button.disabled = true;
                        button.dataset.status = 'completed';
                    } else {
                         button.textContent = 'Start Module';
                         button.classList.remove('button-completed');
                         button.disabled = false;
                         button.dataset.status = 'not_started';
                    }
                });

            } else {
                console.error('Failed to update progress:', result.message || 'Unknown error');
            }

        } catch (error) {
            console.error('Error sending progress update:', error);
        }
    }

    // --- Function to mark content complete via AJAX ---
    async function markContentCompleteAjax(contentId, buttonElement, redirectUrl = null) {
        console.log(`markContentCompleteAjax called. Content ID: ${contentId}, Redirect URL: ${redirectUrl}`); // DEBUG
        const csrfToken = getCsrfToken();
        if (!csrfToken || !contentId) {
            console.error('Missing data for content completion update:', { contentId, csrfToken });
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.innerHTML = buttonElement.dataset.originalHtml || 'Action Failed';
            }
            alert('Error: Could not send completion status. Missing data.');
            return false;
        }

        if (buttonElement && !buttonElement.dataset.originalHtml) {
            buttonElement.dataset.originalHtml = buttonElement.innerHTML;
        }

        try {
            console.log(`Sending AJAX request to /progress/mark-content-complete for content ${contentId}`); // DEBUG
            const response = await fetch(`${BASE_URL}progress/mark-content-complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    content_id: contentId
                })
            });

            console.log(`AJAX response status for /progress/mark-content-complete: ${response.status}`); // DEBUG
            if (!response.ok) {
                 const errorText = await response.text();
                 console.error(`AJAX error response text: ${errorText}`); // DEBUG
                 throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }

            const result = await response.json();
            console.log('AJAX response data for /progress/mark-content-complete:', result); // DEBUG

            if (result.success) {
                console.log('Content marked complete successfully (AJAX result.success is true).'); // DEBUG

                // 1. Update button state
                if (buttonElement && buttonElement.id === 'mark-last-content-complete-btn') {
                    console.log('Updating last complete button to badge.'); // DEBUG
                    const completedBadge = document.createElement('span');
                    completedBadge.className = 'badge badge-success';
                    completedBadge.innerHTML = '<i class="fas fa-check"></i> Completed';
                    buttonElement.parentNode.replaceChild(completedBadge, buttonElement);
                }

                // 2. Update sidebar
                const sidebarItem = document.querySelector(`.content-list-sidebar li[data-content-id="${contentId}"]`);
                if (sidebarItem) {
                    console.log(`Found sidebar item for content ${contentId}, updating styles.`); // DEBUG
                    sidebarItem.classList.add('content-completed');
                    const iconElement = sidebarItem.querySelector('.content-icon i');
                    if (iconElement) {
                        iconElement.className = 'fas fa-check-circle fa-fw';
                        iconElement.style.color = 'var(--jf-green-success)';
                        iconElement.title = 'Completed';
                    }
                } else {
                     console.log(`Sidebar item for content ${contentId} not found via data attribute, trying link.`); // DEBUG
                     const sidebarLink = document.querySelector(`.content-list-sidebar a[href*="/modules/content/"][href$="/${contentId}"]`);
                     if (sidebarLink && sidebarLink.closest('li')) {
                          const listItem = sidebarLink.closest('li');
                          console.log(`Found sidebar item for content ${contentId} via link, updating styles.`); // DEBUG
                          listItem.classList.add('content-completed');
                          const iconElement = listItem.querySelector('.content-icon i');
                          if (iconElement) {
                             iconElement.className = 'fas fa-check-circle fa-fw';
                             iconElement.style.color = 'var(--jf-green-success)';
                             iconElement.title = 'Completed';
                          }
                     } else {
                         console.log(`Sidebar item for content ${contentId} not found.`); // DEBUG
                     }
                }

                // 3. Update Module Progress Bar
                const moduleProgressBar = document.getElementById('module-progress-bar');
                const moduleProgressText = document.getElementById('module-progress-text');
                if (moduleProgressBar && moduleProgressText && result.modulePercentage !== undefined) {
                    console.log(`Updating module progress bar to ${result.modulePercentage}%`); // DEBUG
                    moduleProgressBar.style.width = result.modulePercentage + '%';
                    moduleProgressText.textContent = result.modulePercentage + '% Complete';
                }

                // 4. Update Course Progress Bar
                const courseProgressBar = document.getElementById('course-progress-bar');
                const courseProgressText = document.getElementById('course-progress-text');
                if (courseProgressBar && courseProgressText && result.coursePercentage !== undefined) {
                    console.log(`Updating course progress bar to ${result.coursePercentage}%`); // DEBUG
                    courseProgressBar.style.width = result.coursePercentage + '%';
                    courseProgressText.textContent = result.coursePercentage + '% Complete';
                }

                // 5. Redirect if URL is provided
                if (redirectUrl) {
                    console.log(`AJAX success, attempting redirect to: ${redirectUrl}`); // DEBUG
                    window.location.href = redirectUrl;
                    // Navigation starts here, script execution might stop
                } else {
                    console.log('AJAX success, no redirect URL provided.'); // DEBUG
                    return true; // Indicate success without redirection
                }

            } else {
                console.error('AJAX call succeeded but result.success was false:', result.message || 'Unknown error from server'); // DEBUG
                alert('Error: Could not save completion status. ' + (result.message || 'Please try again.'));
                if (buttonElement) {
                    buttonElement.disabled = false;
                    buttonElement.innerHTML = buttonElement.dataset.originalHtml || 'Action Failed';
                }
                return false; // Indicate failure
            }

        } catch (error) {
            console.error('Error during markContentCompleteAjax fetch or processing:', error); // DEBUG
            alert('An unexpected error occurred while saving your progress. Please try again.');
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.innerHTML = buttonElement.dataset.originalHtml || 'Action Failed';
            }
            return false; // Indicate failure
        }
    }
    // --- End function ---

    // Add event listeners to module action buttons (Start/Continue/Review on Course/Module view)
    moduleActionButtons.forEach(button => {
        // Removed the check for 'completed' status and 'disabled' attribute
        // Now the listener will be added regardless of initial state.
        button.addEventListener('click', function(event) {
            const moduleId = this.dataset.moduleId;
            let currentStatus = this.dataset.status;
            console.log(`Module action button clicked for module ${moduleId}, current status: ${currentStatus}`); // DEBUG
            if (currentStatus === 'not_started') {
                 updateProgress(moduleId, 'in_progress');
            } else {
                console.log(`Navigation proceeds for module ${moduleId} without status update.`);
            }
        });
    });

    // --- Listener for the COMBINED "Complete & Next" button ---
    const completeAndNextButton = document.getElementById('next-lesson-complete-btn');
    if (completeAndNextButton) {
        console.log('course-progress.js: Found #next-lesson-complete-btn, adding listener.');
        completeAndNextButton.dataset.originalHtml = completeAndNextButton.innerHTML;

        completeAndNextButton.addEventListener('click', function(event) {
            event.preventDefault();
            const contentId = this.dataset.contentId;
            const nextUrl = this.dataset.nextUrl;
            const status = this.dataset.status; // Get the status
            console.log(`#next-lesson-complete-btn clicked. Content ID: ${contentId}, Next URL: ${nextUrl}, Status: ${status}`); // DEBUG

            if (status === 'completed') {
                // If already completed, just navigate
                console.log('Content already completed, navigating directly.'); // DEBUG
                window.location.href = nextUrl;
            } else if (contentId && nextUrl) {
                // If incomplete, mark complete via AJAX and then navigate
                console.log('Content incomplete, marking complete via AJAX.'); // DEBUG
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving & Loading...';
                markContentCompleteAjax(contentId, this, nextUrl);
            } else {
                console.error("Data attributes missing on #next-lesson-complete-btn.");
                alert("Error: Could not proceed. Button data missing.");
                this.disabled = false;
                this.innerHTML = this.dataset.originalHtml || 'Error';
            }
        });
    } else {
         console.log('course-progress.js: #next-lesson-complete-btn NOT found.');
    }

    // --- Listener for the "Mark Final Item Complete" button ---
    const markLastCompleteButton = document.getElementById('mark-last-content-complete-btn');
    if (markLastCompleteButton) {
        console.log('course-progress.js: Found #mark-last-content-complete-btn, adding listener.');
        markLastCompleteButton.dataset.originalHtml = markLastCompleteButton.innerHTML;

        markLastCompleteButton.addEventListener('click', function(event) {
            event.preventDefault();
            const contentId = this.dataset.contentId;
            const redirectUrl = this.dataset.redirectUrl;
            console.log(`#mark-last-content-complete-btn clicked. Content ID: ${contentId}, Redirect URL: ${redirectUrl}`); // DEBUG

            if (contentId && redirectUrl) {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                markContentCompleteAjax(contentId, this, redirectUrl);
            } else {
                console.error("Data attributes missing on #mark-last-content-complete-btn.");
                alert("Error: Could not proceed. Button data missing.");
                this.disabled = false;
                this.innerHTML = this.dataset.originalHtml || 'Error';
            }
        });
    } else {
        console.log('course-progress.js: #mark-last-content-complete-btn NOT found.');
    }

});