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
    const testListElement = document.querySelector(".accordion");

    if (!testListElement) return;

    // Check if data exists
    if (!data || !data.tests || data.tests.length === 0) {
        testListElement.innerHTML = "<p>No tests found.</p>";
        return;
    }

    // Clear loading message
    testListElement.classList.remove("loading");

    let testList = "";

    let testIndex = 1;

    for (let test of data.tests) {
        const collapseId = `collapse-${test.id}`;
        const headingId = `heading-${test.id}`;
        const testDate = new Date(test.test_date).toLocaleDateString();

        testList += `
            <div class="accordion-item p-0 pb-3">
                <h2 class="accordion-header" id="${headingId}">
                    <button class="accordion-button accordion-color collapsed d-flex align-items-center justify-content-center" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}"
                            aria-expanded="false"
                            aria-controls="${collapseId}">
                        <div>
                            <div class="fw-bold">Test ${testIndex}: ${test.test_name}</div>
                            <small class="text-muted">Date: ${testDate}</small>
                        </div>
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse"
                     aria-labelledby="${headingId}"
                     data-bs-parent="#testListAccordion">
                    <div class="accordion-body">
                        <p><strong>Description:</strong> ${
                            test.test_description || "No Description"
                        }</p>
        `;
        
        // Targets inside the accordion body
        if (test.targets && test.targets.length > 0) {
            testList += `<ul class="list-group">`;
            for (let target of test.targets) {
                testList += `
                    <li id=target-${target.id} class="list-group-item">
                        <strong>Target Name:</strong> ${
                            target.target_name || "Not found"
                        }<br>
                        <strong>Description:</strong> ${
                            target.target_description || "Not found"
                        }
                    </li>
                `;
            }
            testList += `</ul>`;
        } else {
            testList += `<p><em>No targets available.</em></p>`;
        }

        testList += `
                    </div>
                </div>
            </div>
        `;

        testIndex++;
    }

    testListElement.innerHTML = testList;
}

// Fetch customer tests
fetchCustomerTests();