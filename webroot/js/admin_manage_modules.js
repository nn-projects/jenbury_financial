document.addEventListener('DOMContentLoaded', function () {
    const moduleList = document.getElementById('module-list');
    if (!moduleList) return;

    const courseId = moduleList.dataset.courseId;

    new Sortable(moduleList, {
        handle: '.grab-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function () {
            saveNewOrder(courseId, moduleList);
        }
    });
});

function saveNewOrder(courseId, listElement) {
    const moduleIds = Array.from(listElement.children).map(item => item.dataset.moduleId);
    
    const url = `/admin/admin/update-module-order/${courseId}`;

    // Find the CSRF token from the meta tag
    const csrfToken = document.querySelector('meta[name="csrfToken"]').getAttribute('content');

    fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({ module_ids: moduleIds })
    })
    .then(response => {
        if (!response.ok) {
            // If response is not OK, parse the JSON to get the error message
            return response.json().then(err => {
                throw new Error(err.message || 'Server responded with an error.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Simple, non-blocking feedback. A more advanced solution could use a temporary toast notification.
            console.log('Module order saved successfully!');
            // Optionally, provide visual feedback to the user
            const feedbackElement = document.createElement('div');
            feedbackElement.textContent = 'Order saved!';
            feedbackElement.style.position = 'fixed';
            feedbackElement.style.bottom = '20px';
            feedbackElement.style.right = '20px';
            feedbackElement.style.backgroundColor = '#28a745';
            feedbackElement.style.color = 'white';
            feedbackElement.style.padding = '10px 20px';
            feedbackElement.style.borderRadius = '5px';
            feedbackElement.style.zIndex = '1000';
            document.body.appendChild(feedbackElement);
            setTimeout(() => {
                document.body.removeChild(feedbackElement);
            }, 3000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error saving module order:', error);
        alert('An unexpected error occurred. Please check the console and try again.');
    });
}