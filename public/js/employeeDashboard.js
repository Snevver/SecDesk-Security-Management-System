// Get DOM elements at the top
const selectElement = document.getElementById('customer-select');

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
                                <p class="mb-1"><strong>Customer:</strong> ${customerEmail}</p>
                            </div>
                            <div class="d-flex flex-column gap-2 align-items-end">
                                <button class="btn btn-sm btn-outline-warning" onclick="toggleTestCompletion(${test.id}, false)"><i class="bi bi-arrow-repeat ps-1"></i></button>
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
                                <p class="mb-1"><strong>Customer:</strong> ${customerEmail}</p>
                            </div>
                            <div class="d-flex flex-column gap-2 align-items-end">
                                <button class="btn btn-sm text-nowrap" onclick="toggleTestCompletion(${test.id}, true)"><i class="bi bi-check-lg ps-1"></i></button>
                                <button class="btn btn-sm" onclick='window.location.href = "/edit?test_id=${test.id}"'><i class="bi bi-pencil-fill ps-1"></i></button>
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

getEmployeesTests();
populateSelectElement();

// Event listener for the "Create test" button
const createTestBtn = document.getElementById('create-test-btn');
if (createTestBtn) {
    createTestBtn.addEventListener('click', () => {
        console.log('Create test button clicked'); // Debug log
        if (selectElement) {
            selectElement.classList.remove('d-none');
            console.log('Select element shown'); // Debug log
        } else {
            console.error('selectElement not found');
        }
    });
} else {
    console.error('create-test-btn element not found');
}

// Event listener for the select element
if (selectElement) {
    selectElement.addEventListener('change', (event) => {
        const selectedCustomerID = parseInt(event.target.value);

        console.log('Customer selected:', selectedCustomerID); // Debug log

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
                console.log('Test created:', data); // Debug log
                window.location.href = `/edit?test_id=${data.new_test_id}`;
            })
            .catch((error) => {
                console.error('Error creating test:', error);
                alert('Error creating test: ' + error.message);
            });
    });
} else {
    console.error('customer-select element not found');
}
