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

/**
 * Function to fetch and display user information (email and role).
 */
function fetchUserInfo() {
    fetch(`/api/check-login`, {
        credentials: 'same-origin',
    })
        .then((response) => response.json())
        .then((data) => {
            const emailElement = document.getElementById('userEmail');
            const roleElement = document.getElementById('userRole');

            if (!data.success) {
                emailElement.textContent = 'Unknown';
                roleElement.textContent = 'Unknown';
                return;
            }

            emailElement.textContent = data.user.email || 'Unknown';
            roleElement.textContent = data.user.role || 'Unknown';
        })
        .catch(() => {
            document.getElementById('userEmail').textContent = 'Unknown';
            document.getElementById('userRole').textContent = 'Unknown';
        });
}

// Initialize page
document.addEventListener('DOMContentLoaded', () => {
    fetchUserInfo();
    fetchCustomers();
});

