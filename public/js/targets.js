/**
 * Function to fetch and display targets of a test, given the ID.
 */
function fetchTargets() {
    const urlParams = new URLSearchParams(window.location.search);
    const test_id = urlParams.get('id');

    if (!test_id) {
        console.error('No ID provided in the URL.');
        return;
    }

    fetch(`/api/targets?id=${test_id}`, {
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

            // Don this is for you to style :)
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

document.addEventListener('DOMContentLoaded', function () {
    fetchTargets();
});
