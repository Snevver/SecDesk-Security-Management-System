// Get the base URL of the application
const BASE_URL = window.location.origin + window.location.pathname.split('/public')[0] + '/public';

/**
 * Function to fetch and display customer data.
 */
function fetchCustomers() {
    fetch(`${BASE_URL}/getCustomers`, {
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

// Function to check login status
function checkLoginStatus(callback) {
    fetch(`${BASE_URL}isLoggedIn`, {
        credentials: 'same-origin',
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Display admin info
                document.getElementById('admin-email').textContent =
                    data.email || 'Not found';
                document.getElementById('admin-role').textContent =
                    data.role || 'Not found';
                callback(data);
            } else {
                // Redirect to login if not logged in
                window.location.href = 'login.html';
            }
        })
        .catch((error) => {
            console.error('Error fetching admin info:', error);
            window.location.href = 'login.html';
        });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function () {
    // Check login (with additional role validation) and then fetch customers
    checkLoginStatus(function (data) {
        // Make sure it's an admin or pentester
        if (data.role !== 'admin' && data.role !== 'pentester') {
            window.location.href = 'index.html';
            return;
        }

        fetchCustomers();
    });
});
