//======================================================================
// CUSTOMER DASHBOARD SCRIPT
//======================================================================

/**
 * Fetch customer tests
 */
function fetchCustomerTests() {
    fetch(`/api/tests`, {
        credentials: "same-origin",
    })
        .then((response) => response.json())
        .then((data) => displayCustomerTests(data))
        .catch((error) => {
            const testListElement = document.getElementById("testListContent");
            if (testListElement) {
                testListElement.classList.remove("loading");
                testListElement.innerHTML =
                    "<p>Error loading tests: " + error.message + "</p>";
            }
        });
}

/**
 * Display customer tests on DOM
 * @param {object} tests Test data fetched from database
 * @returns {void}
 */
function displayCustomerTests(data) {
    const testListElement = document.getElementById("testListContent");

    if (!testListElement) return;

    // Check if data exists
    if (!data) {
        testListElement.innerHTML = "<p>No data received.</p>";
        return;
    }

    if (data.length === 0) {
        testListElement.innerHTML = "<p>No tests found.</p>";
        return;
    }

    // Clear loading message
    testListElement.classList.remove("loading");

    let testList = "<ul>";

    // Tests
    for (let test of data.tests) {
        testList += `<div id="test-${test.id}" class="border border-black rounded-md p-4 mb-4">
                        <li>
                            <strong>Test Name:</strong> 
                            ${test.test_name || "Not found"} 
                            <br>
                            <strong>Description:</strong> 
                            ${test.test_description || "Not found"}
                        </li>
                    </div>`;
                    
                    // Targets (this has to be in the dropdown)
                    for (let target of test.targets) {
                        testList += `<div id="target-${target.id}" class="border border-black rounded-md p-4 mb-4">
                                        <li>
                                            <strong>Target Name:</strong> 
                                            ${target.target_name || "Not found"} 
                                            <br>
                                            <strong>Description:</strong> 
                                            ${target.target_description || "Not found"}
                                        </li>
                                    </div>`;
                    }



    }

    testList += "</ul>";

    testListElement.innerHTML = testList;
}

// Fetch customer tests
fetchCustomerTests();
