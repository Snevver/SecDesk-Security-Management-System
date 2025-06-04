//======================================================================
// EMPLOYEE DASHBOARD SCRIPT
//======================================================================

/**
 * Fetch and customer data
 */
function fetchCustomers() {
    fetch(`/api/customers`, {
        credentials: 'same-origin',
    })
        .then((response) => response.json())
        .then((data) => {
            const userListElement = document.getElementById('customers-list');

            if (!data.success) {
                userListElement.innerHTML =
                    '<p>Error loading customers: ' +
                    (data.error || 'Unknown error') +
                    '</p>';
                return;
            }

            if (!data.users || data.users.length === 0) {
                userListElement.innerHTML = '<p>No customers found.</p>';
                return;
            }

            let listElement = '<ul>';

            for (let user of data.users) {
                listElement += `<li>Email: ${user.email} <br> ID: ${user.id}</li>`;
            }

            listElement += '</ul>';

            userListElement.innerHTML = listElement;
        })
        .catch((error) => {
            document.getElementById('userList').innerHTML =
                '<p>Error: ' + error.message + '</p>';
        });
}


function fetchEmployees() {
    fetch(`/api/employees`, {
        credentials: 'same-origin',
    })
        .then((response) => response.json())
        .then((data) => {
            const userListElement = document.getElementById('employees-list');

            if (!data.success) {
                userListElement.innerHTML =
                    '<p>Error loading employees: ' +
                    (data.error || 'Unknown error') +
                    '</p>';
                return;
            }

            if (!data.users || data.users.length === 0) {
                userListElement.innerHTML = '<p>No employees found.</p>';
                return;
            }

            let listElement = '<ul>';

            for (let user of data.users) {
                listElement += `<li>Email: ${user.email} <br> ID: ${user.id}</li>`;
            }

            listElement += '</ul>';

            userListElement.innerHTML = listElement;
        })
        .catch((error) => {
            document.getElementById('userList').innerHTML =
                '<p>Error: ' + error.message + '</p>';
        });
}

fetchCustomers();
fetchEmployees();

// Modal functions
function openEmailModal(title, onSubmit) {
    console.log('Opening modal with title:', title);
    const modal = document.getElementById('emailModal');
    const modalTitle = document.getElementById('modalTitle');
    const emailInput = document.getElementById('emailInput');
    
    if (!modal || !modalTitle || !emailInput) {
        console.error('Modal elements not found');
        return;
    }
    
    modalTitle.textContent = title;
    modal.style.display = 'block';
    emailInput.value = '';
    emailInput.focus();
    
    // Remove any existing event listeners
    const form = document.getElementById('emailForm');
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);
    
    // Add new event listener
    newForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('emailInput').value.trim();
        if (email) {
            onSubmit(email);
            closeEmailModal();
        }
    });
}

function closeEmailModal() {
    document.getElementById('emailModal').style.display = 'none';
    document.getElementById('emailInput').value = '';
}

// Close modal when clicking outside of it
window.addEventListener('click', function(event) {
    const modal = document.getElementById('emailModal');
    if (event.target === modal) {
        closeEmailModal();
    }
});

// Handle escape key to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEmailModal();
    }
});

// Button event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up button listeners');
    
    // Button event listeners
    const createCustomerBtn = document.getElementById('create-customer-btn');
    const createEmployeeBtn = document.getElementById('create-employee-btn');
    
    console.log('Customer button found:', !!createCustomerBtn);
    console.log('Employee button found:', !!createEmployeeBtn);      if (createCustomerBtn) {
        createCustomerBtn.addEventListener('click', () => {
            console.log('Create Customer button clicked');
            openEmailModal('Create New Customer', function(email) {
                console.log('Customer email:', email);

                fetch('/create-customer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email }),
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Customer created successfully!');
                        fetchCustomers();
                    } else {
                        alert('Error creating customer: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error creating customer: ' + error.message);
                });
            });
        });
    } else {
        console.error('Create customer button not found!');
    }    if (createEmployeeBtn) {
        createEmployeeBtn.addEventListener('click', () => {
            console.log('Create employee button clicked');
            openEmailModal('Create New Employee', function(email) {
                console.log('Employee email:', email);

                fetch('/create-employee', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email }),
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Employee created successfully!');
                        fetchEmployees();
                    } else {
                        alert('Error creating employee: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error creating employee: ' + error.message);
                });
            });
        });
    } else {
        console.error('Create employee button not found!');
    }
});
