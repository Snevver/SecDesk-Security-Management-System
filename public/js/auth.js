/**
 * This script handles:
 * - Checking if the user is logged in (using the isLoggedIn() function in the AuthenticationController) and redirecting to the login page if not.
 * - Fetching and displaying user information in the UI.
 */

// Get the base URL of the application
const BASE_URL = '/SecDesk-Security-Management-System/public';

// Common authentication functionality
function checkLoginStatus(callback) {
    console.log('Checking login status...');

    console.log('BASE_URL:', BASE_URL);
    console.log('Redirecting to:', BASE_URL + '/login');
    
    fetch(BASE_URL + '/isLoggedIn', {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then((response) => {
        // Check if response is OK
        if (!response.ok) {
            console.error('Response not OK:', response.status);
            throw new Error('Authentication check failed');
        }
        
        // Check content type to avoid HTML parsing errors
        // const contentType = response.headers.get('Content-Type');
        // if (!contentType || !contentType.includes('application/json')) {
        //     console.error('Invalid content type:', contentType);
        //     throw new Error('Invalid response format');
        // }
        
        return response.json();
    })
    .then((data) => {
        console.log('Login status data:', data);
        
        if (!data.success) {
            console.log('User not logged in, redirecting to login page');
            // Ensure the redirection uses BASE_URL
            window.location.href = BASE_URL + '/login';
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
            window.location.href = BASE_URL + '/login';
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