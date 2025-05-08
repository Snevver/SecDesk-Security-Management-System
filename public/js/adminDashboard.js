/**
 * This script handles:
 * - Fetching and displaying a list of customers from the server.
 * - Checking the logged in users role to check if they have access to the admin panel, if not, they get send to the index.html.
 */


// Function to fetch customers
function fetchCustomers() {
    fetch('getCustomers', {
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
    fetch('isLoggedIn', {
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
