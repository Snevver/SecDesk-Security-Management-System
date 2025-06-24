function getEmployeesTests() {
  fetch(`/api/employee-tests`, {
    credentials: "same-origin",
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      const completedTestsContainer =
        document.getElementById("completed-reports");
      const inProgressTestsContainer = document.getElementById(
        "reports-in-progress"
      );

      // Clear existing content
      completedTestsContainer.innerHTML = "";
      inProgressTestsContainer.innerHTML = "";

      // Display completed tests
      if (data.completedTests && data.completedTests.length > 0) {
        let completedHTML = "";
        let testIndex = 1;
        data.completedTests.forEach((test) => {
          const testDate = new Date(test.test_date).toLocaleDateString();
          completedHTML += `
      <div id="test-${test.id}" class="test-item p-0 pb-3">
        <div class="test-button accordion-color rounded d-flex justify-content-between align-items-start w-100 p-3 shadow-sm">
          <div class="text-start pe-3 flex-grow-1">
            <div class="fw-bold fs-5">${test.test_name}</div>
            <small class="text-muted d-block mb-1">Date: ${testDate}</small>
            <p class="mb-1"><strong>Description:</strong> ${test.test_description}</p>
            <p class="mb-0"><strong>Status:</strong> <span>✅ Completed</span></p>
          </div>
          <div class="d-flex flex-column gap-2 align-items-end">
            <button class="btn btn-sm btn-outline-warning" onclick="toggleTestCompletion(${test.id}, false)">Mark as In Progress <i class="bi bi-arrow-repeat ps-1"></i></button>
          </div>
        </div>
      </div>
    `;
        });
        completedTestsContainer.innerHTML = completedHTML;
      } else {
        completedTestsContainer.innerHTML = "<p>No completed tests found.</p>";
      }

      // Display non-completed tests
      if (data.nonCompletedTests && data.nonCompletedTests.length > 0) {
        let inProgressHTML = "";
        let testIndex = 1;
        data.nonCompletedTests.forEach((test) => {
          const testDate = new Date(test.test_date).toLocaleDateString();
          inProgressHTML += `
      <div id="test-${test.id}" class="test-item p-0 pb-3">
        <div class="test-button accordion-color rounded d-flex justify-content-between align-items-start w-100 p-3 shadow-sm">
          <div class="text-start pe-3 flex-grow-1">
            <div class="fw-bold fs-5">${test.test_name}</div>
            <small class="text-muted d-block mb-1">Date: ${testDate}</small>
            <p class="mb-1"><strong>Description:</strong> ${test.test_description}</p>
            <p class="mb-0"><strong>Status:</strong> <span>🔄 In Progress</span></p>
          </div>
          <div class="d-flex flex-column gap-2 align-items-end">
            <button class="btn btn-sm text-nowrap" onclick="toggleTestCompletion(${test.id}, true)">Mark as Completed <i class="bi bi-check-lg ps-1"></i></button>
            <button class="btn btn-sm" onclick='window.location.href = "/edit?test_id=${test.id}"'>Edit test <i class="bi bi-pencil-fill ps-1"></i></button>
          </div>
        </div>
      </div>
    `;
        });
        inProgressTestsContainer.innerHTML = inProgressHTML;
      } else {
        inProgressTestsContainer.innerHTML = "<p>No tests in progress.</p>";
      }
    })
    .catch((error) => {
      console.error("Error fetching tests:", error);
      document.getElementById("completed-reports").innerHTML =
        "<p>Error loading tests: " + error.message + "</p>";
      document.getElementById("reports-in-progress").innerHTML =
        "<p>Error loading tests: " + error.message + "</p>";
    });
}

// Function to toggle test completion status
function toggleTestCompletion(testId, completed) {
  fetch("/api/update-test-completion", {
    method: "POST",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      test_id: testId,
      completed: completed,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        getEmployeesTests();
      } else {
        alert("Error updating test status: " + (data.error || "Unknown error"));
      }
    })
    .catch((error) => {
      console.error("Error updating test completion:", error);
      alert("Error updating test status: " + error.message);
    });
}

// Populate the select element with customer emails
function populateSelectElement() {
  fetch("/api/customers", {
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      for (const user of data.users) {
        const optionElement = document.createElement("option");
        optionElement.value = user.id;
        optionElement.textContent = user.email;
        selectElement.appendChild(optionElement);
      }
    });
}

const selectElement = document.getElementById("customer-select");

getEmployeesTests();
populateSelectElement();

// Event listener for the "Create Report" button
document.getElementById("create-report-btn").addEventListener("click", () => {
  selectElement.classList.remove("d-none");
});

// Event listener for the select element
selectElement.addEventListener("change", (event) => {
  const selectedCustomerID = parseInt(event.target.value);

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
      window.location.href = `/edit?test_id=${newTestID}`;
    });
});
