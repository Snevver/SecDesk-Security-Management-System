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
                let completedHTML = "<ul>";
                data.completedTests.forEach((test) => {
                    completedHTML += `
                        <li>
                            <h3>${test.test_name}</h3>
                            <p><strong>Description:</strong> ${
                                test.test_description
                            }</p>
                            <p><strong>Test Date:</strong> ${new Date(
                                test.test_date
                            ).toLocaleDateString()}</p>
                            <p><strong>Status:</strong><span>✅ Completed</span></p>
                            <button onclick="toggleTestCompletion(${
                                test.id
                            }, false)">Mark as In Progress</button>
                        </li>
                    `;
                });
                completedHTML += "</ul>";
                completedTestsContainer.innerHTML = completedHTML;
            } else {
                completedTestsContainer.innerHTML =
                    "<p>No completed tests found.</p>";
            }

            // Display non-completed tests
            if (data.nonCompletedTests && data.nonCompletedTests.length > 0) {
                let inProgressHTML = "<ul>";
                data.nonCompletedTests.forEach((test) => {
                    inProgressHTML += `
                        <li>
                            <h3>${test.test_name}</h3>
                            <p><strong>Description:</strong> ${
                                test.test_description
                            }</p>
                            <p><strong>Test Date:</strong> ${new Date(
                                test.test_date
                            ).toLocaleDateString()}</p>
                            <p><strong>Status:</strong> <span>🔄 In Progress</span></p>
                            <button onclick="toggleTestCompletion(${
                                test.id
                            }, true)">Mark as Completed</button>
                            <button onclick='window.location.href = "/edit?test_id=${
                                test.id
                            }"';>Edit test</button>
                        </li>
                    `;
                });
                inProgressHTML += "</ul>";
                inProgressTestsContainer.innerHTML = inProgressHTML;
            } else {
                inProgressTestsContainer.innerHTML =
                    "<p>No tests in progress.</p>";
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
                alert(
                    "Error updating test status: " +
                        (data.error || "Unknown error")
                );
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
