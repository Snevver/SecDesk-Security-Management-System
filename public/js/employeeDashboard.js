async function getEmployeesTests() {
    try {
        const response = await fetch(`/api/get-all-employee-tests`, {
            credentials: 'same-origin',
        });
        const data = await response.json();
        console.log(data);

        const completedTestsContainer =
            document.getElementById('completed-tests');
        const inProgressTestsContainer =
            document.getElementById('tests-in-progress');

        // Clear existing content
        completedTestsContainer.innerHTML = '';
        inProgressTestsContainer.innerHTML = '';

        // Display completed tests
        if (data.completedTests && data.completedTests.length > 0) {
            let completedHTML = '';
            for (const test of data.completedTests) {
                const testDate = new Date(test.test_date).toLocaleDateString();
                const customerEmail = await getCustomerEmail(test.customer_id);
                completedHTML += `
                    <div id="test-${test.id}" class="test-item p-0 pb-3">
                        <div class="test-button accordion-color rounded d-flex justify-content-between align-items-start w-100 p-3 shadow-sm">
                            <div class="text-start pe-3 flex-grow-1">
                                <div class="fw-bold fs-5">${test.test_name}</div>
                                <small class="text-muted d-block mb-1">Date: ${testDate}</small>
                                <p class="mb-1"><strong>Description:</strong> ${test.test_description}</p>
                                <p class="mb-1"><strong>Customer:</strong> ${customerEmail}</p>
                                <p class="mb-0"><strong>Status:</strong> <span>✅ Completed</span></p>
                            </div>
                            <div class="d-flex flex-column gap-2 align-items-end">
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleTestCompletion(${test.id}, false)">Mark as In Progress <i class="bi bi-arrow-repeat ps-1"></i></button>
                            </div>
                        </div>
                    </div>
                `;
            }
            completedTestsContainer.innerHTML = completedHTML;
        } else {
            completedTestsContainer.innerHTML =
                '<p>No completed tests found.</p>';
        }

        // Display non-completed tests
        if (data.nonCompletedTests && data.nonCompletedTests.length > 0) {
            let inProgressHTML = '';
            for (const test of data.nonCompletedTests) {
                const testDate = new Date(test.test_date).toLocaleDateString();
                const customerEmail = await getCustomerEmail(test.customer_id);
                inProgressHTML += `
                    <div id="test-${test.id}" class="test-item p-0 pb-3">
                        <div class="test-button accordion-color rounded d-flex justify-content-between align-items-start w-100 p-3 shadow-sm">
                            <div class="text-start pe-3 flex-grow-1">
                                <div class="fw-bold fs-5">${test.test_name}</div>
                                <small class="text-muted d-block mb-1">Date: ${testDate}</small>
                                <p class="mb-1"><strong>Description:</strong> ${test.test_description}</p>
                                <p class="mb-1"><strong>Customer:</strong> ${customerEmail}</p>
                                <p class="mb-0"><strong>Status:</strong> <span>🔄 In Progress</span></p>
                            </div>
                            <div class="d-flex flex-column gap-2 align-items-end">
                                <button class="btn btn-sm text-nowrap" onclick="toggleTestCompletion(${test.id}, true)">Mark as Completed <i class="bi bi-check-lg ps-1"></i></button>
                                <button class="btn btn-sm" onclick='window.location.href = "/edit?test_id=${test.id}"'>Edit test <i class="bi bi-pencil-fill ps-1"></i></button>
                            </div>
                        </div>
                    </div>
                `;
            }
            inProgressTestsContainer.innerHTML = inProgressHTML;
        } else {
            inProgressTestsContainer.innerHTML = '<p>No tests in progress.</p>';
        }
    } catch (error) {
        console.error('Error fetching tests:', error);
        document.getElementById('completed-tests').innerHTML =
            '<p>Error loading tests: ' + error.message + '</p>';
        document.getElementById('tests-in-progress').innerHTML =
            '<p>Error loading tests: ' + error.message + '</p>';
    }
}

// Function to toggle test completion status
function toggleTestCompletion(testId, completed) {
    fetch('/api/update-test-completion', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
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
                    'Error updating test status: ' +
                        (data.error || 'Unknown error'),
                );
            }
        })
        .catch((error) => {
            console.error('Error updating test completion:', error);
            alert('Error updating test status: ' + error.message);
        });
}

// Populate the select element with customer emails
function populateSelectElement() {
    fetch('/api/get-all-customers', {
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then((response) => response.json())
        .then((data) => {
            for (const user of data.users) {
                const optionElement = document.createElement('option');
                optionElement.value = user.id;
                optionElement.textContent = user.email;
                selectElement.appendChild(optionElement);
            }
        });
}

// Function to get customer email
async function getCustomerEmail(customerID) {
    try {
        const response = await fetch(
            `/api/get-customer-email?customer_id=${customerID}`,
            {
                credentials: 'same-origin',
            },
        );
        const data = await response.json();
        return data.email || 'Unknown Customer';
    } catch (error) {
        console.error('Error fetching customer email:', error);
        return 'Unknown Customer';
    }
}

const selectElement = document.getElementById('customer-select');

getEmployeesTests();
populateSelectElement();

// Event listener for the "Create test" button
document.getElementById('create-test-btn').addEventListener('click', () => {
    selectElement.classList.remove('d-none');
});

// Event listener for the select element
selectElement.addEventListener('change', (event) => {
    const selectedCustomerID = parseInt(event.target.value);

    fetch('/create-test', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            customer_id: selectedCustomerID,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            window.location.href = `/edit?test_id=${data.new_test_id}`;
        });
});
