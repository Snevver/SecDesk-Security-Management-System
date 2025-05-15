//======================================================================
// CUSTOMER DASHBOARD SCRIPT
//======================================================================

/**
 * Log debug messages
 * @param {*} message the debug message to be displayed
 * @param {*} object
 */
function debugLog(message, object = null) {
    const debugOutputElement = document.getElementById("debug-output");
    if (debugOutputElement) {
        debugOutputElement.style.display = "block";
        if (object) {
            debugOutputElement.textContent +=
                message + ": " + JSON.stringify(object, null, 2) + "\n\n";
        } else {
            debugOutputElement.textContent += message + "\n";
        }
    }
    console.log(message, object || "");
}

/**
 * Fetch and display customer tests
 */
function fetchCustomersTests() {
    fetch(`/api/tests`, {
        credentials: "same-origin",
    })
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            const testListElement = document.getElementById("testListContent");

            if (!testListElement) {
                return;
            }

            // Clear loading message
            testListElement.classList.remove("loading");

            // Check if data exists
            if (!data) {
                testListElement.innerHTML = "<p>No data received.</p>";
                return;
            }

            // Extract tests from the data
            let tests;
            tests = data;

            if (tests.length === 0) {
                testListElement.innerHTML = "<p>No tests found.</p>";
                return;
            }

            let html = "<ul>";
            tests.forEach((test) => {
                html += `<div id="test-${
                    test.id
                }" class="border border-black rounded-md p-4 mb-4">
                            <li>
                                <strong>Test Name:</strong> ${
                                    test.test_name || "Not found"
                                } <br>
                                <strong>Description:</strong> ${
                                    test.test_description || "Not found"
                                }
                            </li>
                        </div>`;
            });
            html += "</ul>";

            testListElement.innerHTML = html;
        })
        .catch((error) => {
            const testListElement = document.getElementById("testListContent");
            if (testListElement) {
                testListElement.classList.remove("loading");
                testListElement.innerHTML =
                    "<p>Error loading tests: " + error.message + "</p>";
            }
        });
}

// Fetch customer tests
fetchCustomersTests();

// Add click listeners to test items
document
    .getElementById("testListContent")
    .addEventListener("click", (event) => {
        // Find the closest test div parent
        const testDiv = event.target.closest('[id^="test-"]');

        // Redirect to the targets page if a test div is clicked and pass the test ID
        if (testDiv) {
            const testId = testDiv.id.replace("test-", "");
            window.location.href = `/targets?id=${testId}`;
        }
    });
