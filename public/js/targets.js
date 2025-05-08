/**
 * This script handles:
 * - Fetching and displaying the targets for a specific test from the database.
 */

// Function to fetch targets from the database with the id in the URL
function fetchTargets() {
    const urlParams = new URLSearchParams(window.location.search);
    const test_id = urlParams.get('id');

    if (!test_id) {
        console.error('No ID provided in the URL.');
        return;
    }

    fetch(`getTargets?id=${test_id}`, {
        credentials: 'same-origin',
    })
        .then((response) => response.json())
        .then((data) => {
            const targetListElement = document.getElementById('target-list');

            if (!data.success) {
                targetListElement.innerHTML =
                    '<p>Error loading targets: ' +
                    (data.error || 'Unknown error') +
                    '</p>';
                return;
            }

            if (!data.targets || data.targets.length === 0) {
                targetListElement.innerHTML = '<p>No targets found.</p>';
                return;
            }

            let html = '<ul>';
            data.targets.forEach((target) => {
                html += `<div id="target-${target.id}">
                            <li>
                                <strong>Target Name:</strong> ${
                                    target.target_name || 'Not found'
                                } <br>
                                <strong>Description:</strong> ${
                                    target.target_description || 'Not found'
                                }
                            </li>
                        </div>`;
            });
            html += '</ul>';

            targetListElement.innerHTML = html;
        })
        .catch((error) => {
            const targetListElement = document.getElementById('target-list');
            if (targetListElement) {
                targetListElement.innerHTML =
                    '<p>Error: ' + error.message + '</p>';
            }
        });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function () {
    // Check login and then fetch targets
    checkLoginStatus(fetchTargets);
});
