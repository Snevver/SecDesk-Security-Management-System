// Get DOM elements at the top
const selectElement = document.getElementById("customer-select");

async function getEmployeesTests() {
  try {
    const response = await fetch(`/api/get-all-employee-tests`, {
      credentials: "same-origin",
    });
    const data = await response.json();
    console.log(data);

    const completedTestsContainer = document.getElementById("completed-tests");
    const inProgressTestsContainer =
      document.getElementById("tests-in-progress");

    // Clear existing content
    // completedTestsContainer.innerHTML = '';
    // inProgressTestsContainer.innerHTML = '';

    // Display completed tests
    if (data.completedTests && data.completedTests.length > 0) {
      let completedHTML = "";
      for (const test of data.completedTests) {
        const testDate = new Date(test.test_date).toLocaleDateString();
        const customerEmail = await getCustomerEmail(test.customer_id);
        completedHTML += `
                    <div id="test-${test.id}" class="test-item p-0 pb-3">
                        <div class="test-button accordion-color rounded d-flex justify-content-between align-items-start w-100 p-3 shadow-sm">
                            <div class="text-start pe-3 flex-grow-1">
                                <div class="fw-bold fs-5">${test.test_name}</div>
                                <p class="mb-1"><strong>Customer:</strong> ${customerEmail}</p>
                            </div>
                            <div class="d-flex flex-column gap-2 align-items-end">
                                <button class="btn btn-sm text-nowrap" onclick="toggleTestCompletion(${test.id}, false)"><span>Mark as in progress</span><i class="bi bi-arrow-repeat ps-1"></i></button>
                            </div>
                        </div>
                    </div>
                `;
      }
      completedTestsContainer.innerHTML = completedHTML;
    } else {
      completedTestsContainer.innerHTML = "<p>No completed tests found.</p>";
    }

    // Display non-completed tests
    if (data.nonCompletedTests && data.nonCompletedTests.length > 0) {
      let inProgressHTML = "";
      for (const test of data.nonCompletedTests) {
        const testDate = new Date(test.test_date).toLocaleDateString();
        const customerEmail = await getCustomerEmail(test.customer_id);
        inProgressHTML += `
                    <div id="test-${test.id}" class="test-item p-0 pb-3">
                        <div class="test-button accordion-color rounded d-flex justify-content-between align-items-start w-100 p-3 shadow-sm">
                            <div class="text-start pe-3 flex-grow-1">
                                <div class="fw-bold fs-5">${test.test_name}</div>
                                <p class="mb-1"><strong>Customer:</strong> ${customerEmail}</p>
                            </div>
                            <div class="d-flex flex-column gap-2 align-items-end">
                                <button class="btn btn-sm text-nowrap" onclick="toggleTestCompletion(${test.id}, true)"><span>Mark as completed</span><i class="bi bi-check-lg ps-1"></i></button>
                                <button class="btn btn-sm text-nowrap" onclick='window.location.href = "/edit?test_id=${test.id}"'><span>Edit test</span><i class="bi bi-pencil-fill ps-1"></i></button>
                            </div>
                        </div>
                    </div>
                `;
      }
      inProgressTestsContainer.innerHTML = inProgressHTML;
    } else {
      inProgressTestsContainer.innerHTML = "<p>No tests in progress.</p>";
    }
  } catch (error) {
    console.error("Error fetching tests:", error);
    document.getElementById("completed-tests").innerHTML =
      "<p>Error loading tests: " + error.message + "</p>";
    document.getElementById("tests-in-progress").innerHTML =
      "<p>Error loading tests: " + error.message + "</p>";
  }
}

// Function to toggle test completion status
async function toggleTestCompletion(testId, completed) {
  const spinner = document.getElementById("pageSpinner");
  if (spinner) {
    spinner.classList.add("d-flex");
    spinner.classList.remove("d-none");
  }

  try {
    const response = await fetch("/api/update-test-completion", {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        test_id: testId,
        completed: completed,
      }),
    });

    const data = await response.json();

    if (data.success) {
      if (typeof getEmployeesTests === "function") {
        await getEmployeesTests();
      }
    } else {
      alert("Error updating test status: " + (data.error || "Unknown error"));
    }
  } catch (error) {
    console.error("Error updating test completion:", error);
    alert("Error updating test status: " + error.message);
  } finally {
    if (spinner) {
      spinner.classList.remove("d-flex");
      spinner.classList.add("d-none");
    }
  }
}

// Populate the Bootstrap dropdown with customer emails
function populateCustomerDropdown() {
  fetch("/api/get-all-customers", {
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("customer-dropdown");
      dropdown.innerHTML = ""; // Clear loading text
      if (data.users && data.users.length > 0) {
        data.users.forEach((user) => {
          const li = document.createElement("li");
          const a = document.createElement("a");
          a.className = "dropdown-item";
          a.href = "#";
          a.textContent = user.email;
          a.dataset.customerId = user.id;
          li.appendChild(a);
          dropdown.appendChild(li);
        });
      } else {
        dropdown.innerHTML =
          '<li><span class="dropdown-item-text text-muted">No customers found</span></li>';
      }
    });
}

// Function to get customer email
async function getCustomerEmail(customerID) {
  try {
    const response = await fetch(
      `/api/get-customer-email?customer_id=${customerID}`,
      {
        credentials: "same-origin",
      }
    );
    const data = await response.json();
    return data.email || "Unknown Customer";
  } catch (error) {
    console.error("Error fetching customer email:", error);
    return "Unknown Customer";
  }
}

getEmployeesTests();
populateCustomerDropdown();

// Event listener for the "Create test" button
const createTestBtn = document.getElementById("create-test-btn");
if (createTestBtn) {
  createTestBtn.addEventListener("click", () => {
    console.log("Create test button clicked"); // Debug log
    if (selectElement) {
      selectElement.classList.remove("d-none");
      console.log("Select element shown"); // Debug log
    } else {
      console.error("selectElement not found");
    }
  });
} else {
  console.error("create-test-btn element not found");
}

// Handle click on dropdown items
document
  .getElementById("customer-dropdown")
  .addEventListener("click", function (e) {
    if (
      e.target.classList.contains("dropdown-item") &&
      e.target.dataset.customerId
    ) {
      // Show the page spinner
      const spinner = document.getElementById("pageSpinner");
      if (spinner) {
        spinner.classList.add("d-flex");
        spinner.classList.remove("d-none");
      }

      const selectedCustomerID = parseInt(e.target.dataset.customerId);
      fetch("/create-test", {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          customer_id: selectedCustomerID,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          window.location.href = `/edit?test_id=${data.new_test_id}`;
        })
        .catch((error) => {
          if (spinner) spinner.classList.add("d-none");
          console.error("Error creating test:", error);
          alert("Error creating test: " + error.message);
        });
    }
  });
