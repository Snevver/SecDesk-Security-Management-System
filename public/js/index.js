/**
 * This script handles:
 * - Fetching and displaying the logged in users tests from the database.
 * - Redirecting to the targets.html page when a test is clicked and passing the test ID as a parameter.
 */

// Helper function for debugging
function debugLog(message, obj = null) {
    const debugOutput = document.getElementById('debug-output');
    if (debugOutput) {
        debugOutput.style.display = 'block';
        if (obj) {
            debugOutput.textContent += message + ': ' + JSON.stringify(obj, null, 2) + '\n\n';
        } else {
            debugOutput.textContent += message + '\n';
        }
    }
    console.log(message, obj || '');
}

// Fetch test data
function fetchCustomersTests() {
    fetch('getCustomersTests', {
        credentials: 'same-origin',
    })
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            const testListElement = document.getElementById('test-list');

            if (!testListElement) {
                return;
            }

            // Clear loading message
            testListElement.classList.remove('loading');

            // Check if data exists
            if (!data) {
                testListElement.innerHTML = '<p>No data received.</p>';
                return;
            }

            // Extract tests from the data
            let tests;
            tests = data;

            if (tests.length === 0) {
                testListElement.innerHTML = '<p>No tests found.</p>';
                return;
            }

            let html = '<ul>';
            tests.forEach((test) => {
                html += `<div id="test-${test.id}">
                            <li>
                                <strong>Test Name:</strong> ${
                                    test.test_name || 'Not found'
                                } <br>
                                <strong>Description:</strong> ${
                                    test.test_description || 'Not found'
                                }
                            </li>
                        </div>`;
            });
            html += '</ul>';

            testListElement.innerHTML = html;
        })
        .catch((error) => {
            const testListElement = document.getElementById('test-list');
            if (testListElement) {
                testListElement.classList.remove('loading');
                testListElement.innerHTML =
                    '<p>Error loading tests: ' + error.message + '</p>';
            }
        });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function () {
    // Check login and then fetch customer tests
    checkLoginStatus(fetchCustomersTests);

    // Add click listeners to test items
    const testList = document.getElementById('test-list');
    if (testList) {
        testList.addEventListener('click', function (event) {
            // Find the closest test div parent
            const testDiv = event.target.closest('[id^="test-"]');
            if (testDiv) {
                const testId = testDiv.id.replace('test-', '');
                window.location.href = `targets.html?id=${testId}`;
            }
        });
    }
});