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
                <div class="test-item p-0 pb-3">
                    <h2 id="${headingId}">
                        <button class="test-button accordion-color rounded d-flex align-items-center justify-content-start text-start" type="button">
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
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const testId = this.id.replace("test-", "");
      window.location.href = `/targets?test_id=${testId}`;
    });
  });
}

// Fetch customer tests
fetchCustomerTests();
