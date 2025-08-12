/**
 * JavaScript for Admin Manage Users Page
 * Handles client-side search/filter and table sorting.
 */
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('user-search');
    const tableBody = document.getElementById('user-list-body');
    const userRows = tableBody ? Array.from(tableBody.querySelectorAll('tr.user-row')) : [];
    const sortHeaders = document.querySelectorAll('.user-table th[data-sort-by]');
    const searchCountSpan = document.getElementById('search-results-count'); // Optional: for accessibility

    // --- Search Functionality ---
    if (searchInput && tableBody && userRows.length > 0) {
        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            userRows.forEach(row => {
                const nameCell = row.querySelector('.user-name');
                const emailCell = row.querySelector('.user-email');
                const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
                const emailText = emailCell ? emailCell.textContent.toLowerCase() : '';

                if (nameText.includes(searchTerm) || emailText.includes(searchTerm)) {
                    row.style.display = ''; // Show row
                    visibleCount++;
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });

            // Optional: Update search results count for screen readers
            if (searchCountSpan) {
                searchCountSpan.textContent = `${visibleCount} user(s) found.`;
            }
        });
    }

    // --- Sorting Functionality ---
    if (sortHeaders.length > 0 && tableBody && userRows.length > 0) {
        sortHeaders.forEach(header => {
            const sortButton = header.querySelector('button.sort-button');
            if (sortButton) {
                sortButton.addEventListener('click', () => {
                    const sortBy = header.dataset.sortBy;
                    let currentSort = header.getAttribute('aria-sort');
                    let direction = 'ascending'; // Default to ascending

                    // Determine new sort direction
                    if (currentSort === 'ascending') {
                        direction = 'descending';
                    } else if (currentSort === 'descending') {
                        direction = 'ascending'; // Cycle back or could reset to 'none'
                    } // else it was 'none', so default 'ascending' is fine

                    // Reset other headers' sort state
                    sortHeaders.forEach(h => {
                        if (h !== header) {
                            h.setAttribute('aria-sort', 'none');
                            const otherIcon = h.querySelector('.sort-icon');
                            if (otherIcon) {
                                otherIcon.className = 'sort-icon fas fa-sort';
                            }
                        }
                    });

                    // Update current header's sort state and icon
                    header.setAttribute('aria-sort', direction);
                    const icon = header.querySelector('.sort-icon');
                    if (icon) {
                        icon.className = `sort-icon fas ${direction === 'ascending' ? 'fa-sort-up' : 'fa-sort-down'}`;
                    }

                    // Perform the sort
                    sortTable(sortBy, direction);
                });
            }
        });

        function sortTable(columnKey, direction) {
            const modifier = direction === 'ascending' ? 1 : -1;

            const sortedRows = userRows.sort((rowA, rowB) => {
                const cellA = rowA.querySelector(`.user-${columnKey}`);
                const cellB = rowB.querySelector(`.user-${columnKey}`);

                let valA = cellA ? cellA.textContent.trim() : '';
                let valB = cellB ? cellB.textContent.trim() : '';

                // Basic case-insensitive string comparison
                // Add specific logic for dates or numbers if needed
                if (columnKey === 'created') {
                    // Attempt date comparison (simple, assumes 'M d, Y' format consistently)
                    // More robust parsing might be needed for different locales/formats
                    const dateA = Date.parse(valA);
                    const dateB = Date.parse(valB);
                    if (!isNaN(dateA) && !isNaN(dateB)) {
                        return (dateA - dateB) * modifier;
                    }
                    // Fallback to string compare if dates are invalid
                }

                // Default string comparison
                valA = valA.toLowerCase();
                valB = valB.toLowerCase();

                if (valA < valB) return -1 * modifier;
                if (valA > valB) return 1 * modifier;
                return 0;
            });

            // Re-append rows in sorted order
            tableBody.innerHTML = ''; // Clear existing rows
            sortedRows.forEach(row => tableBody.appendChild(row));
        }
    }

}); // End DOMContentLoaded