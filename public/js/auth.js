// Get the base URL of the application
const BASE_URL = window.location.origin + window.location.pathname.split('/public')[0] + '/public';

/**
 * Function to check if the user is logged in. If not, redirect to the login page.
 * @param {Function} callback - Optional callback function to execute after checking login status
*/
function checkLoginStatus(callback) {
    // Fetch the login endpoint to check if the user is logged in, using the authentication controller
    fetch(`${BASE_URL}/isLoggedIn`, {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then((response) => {
        // Check if response is OK
        if (!response.ok) {
            throw new Error('Authentication check failed');
        }
        
        // Check content type to avoid HTML parsing errors
        const contentType = response.headers.get('Content-Type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response format. Expected JSON. Received: ' + contentType);
        }
        
        return response.json();
    })
    .then((data) => {        
        if (!data.success) {
            window.location.href = `${BASE_URL}/login`;
            return;
        }

        console.log('User is logged in:', data.email);
        
        // User is logged in, update UI elements
        updateUserInfo(data);

        // Execute the callback function if provided
        if (typeof callback === 'function') {
            callback(data);
        }
    })
    .catch((error) => {
        console.error('Error checking login status:', error);
        // Only redirect if we're not already on the login page
        if (!window.location.pathname.includes('login')) {
            // Ensure the redirection uses BASE_URL
            window.location.href = `${BASE_URL}/login`;
        }
    });
}

// Helper function to update user info in the UI
function updateUserInfo(data) {
    console.log('Updating UI with user info');
    
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

// Check login when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    checkLoginStatus();
});