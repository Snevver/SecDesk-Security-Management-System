/**
 * This script handles:
 * - Checking if the user is logged in (using the isLoggedIn() function in the AuthenticationController) and redirecting to the login page if not.
 * - Fetching and displaying user information in the UI.
 */

// Common authentication functionality
function checkLoginStatus(callback) {
    fetch('isLoggedIn', {
        credentials: 'same-origin',
    })
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                // User is not logged in, redirect to login page
                window.location.href = 'login';
                return;
            }

            // User is logged in, update UI elements
            updateUserInfo(data);

            // Execute the callback function if provided
            if (typeof callback === 'function') {
                callback(data);
            }
        })
        .catch((error) => {
            console.error('Error checking login status:', error);
            window.location.href = 'login';
        });
}

// Helper function to update user info in the UI
function updateUserInfo(data) {
    // Update email display
    const userEmailEl =
        document.getElementById('user-email') ||
        document.getElementById('admin-email');
    if (userEmailEl) {
        userEmailEl.textContent = data.email || 'Unknown';
    }

    // Update role display
    const userRoleEl =
        document.getElementById('user-role') ||
        document.getElementById('admin-role');
    if (userRoleEl) {
        userRoleEl.textContent = data.role || 'Unknown';
    }

    // Update user ID display
    const userIdEl = document.getElementById('user-id');
    if (userIdEl) {
        userIdEl.textContent = data.user_id || 'Unknown';
    }
}
