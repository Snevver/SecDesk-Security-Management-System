/**
 * Function to fetch and display customer data.
 */
function fetchCustomers() {
    fetch(`/api/customers`, {
        credentials: 'same-origin',
    })
        .then((response) => response.json())
        .then((data) => {
            const userListElement = document.getElementById('userList');

            if (!data.success) {
                userListElement.innerHTML =
                    '<p>Error loading users: ' +
                    (data.error || 'Unknown error') +
                    '</p>';
                return;
            }

            if (!data.users || data.users.length === 0) {
                userListElement.innerHTML = '<p>No users found.</p>';
                return;
            }

            let html = '<ul>';
            data.users.forEach((user) => {
                html += `<li>Email: ${user.email} <br> ID: ${user.id}</li>`;
            });
            html += '</ul>';

            userListElement.innerHTML = html;
        })
        .catch((error) => {
            document.getElementById('userList').innerHTML =
                '<p>Error: ' + error.message + '</p>';
        });
}

// Initialize page
document.addEventListener('DOMContentLoaded', () => fetchCustomers());

