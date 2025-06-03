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
