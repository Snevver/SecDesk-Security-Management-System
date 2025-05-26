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
            <a id="test-${test.id}" class="text-decoration-none">
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
                </div>
            </a>
        `;

        testIndex++;
    }

    testListElement.innerHTML = testList;

    // Add click listeners to test links
    const testLinks = testListElement.querySelectorAll("a[id^='test-']");
    testLinks.forEach((link) => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const testId = this.id.replace('test-', '');
            window.location.href = `/targets?id=${testId}`;
        });
    });
}

// Fetch customer tests
fetchCustomerTests();

document.querySelector(".accordion").addEventListener("click", function(event) {
    const targetLi = event.target.closest("li[id^='target-']");
    if (targetLi) {
        const targetId = targetLi.id.split("-")[1];
        fetch(`api/vulnerabilities/${targetId}`)
            .then((response) => response.json())
            .then((data) => {
                // Handle the data received from the server
                console.log(data);
            })
            .catch((error) => {
                console.error("Error fetching target vulnerabilities:", error);
            });
    }
});