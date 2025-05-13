/**
 * Function to check if the user is logged in. If not, redirect to the login page.
*/
function checkLoginStatus() {
    // Fetch the login endpoint to check if the user is logged in, using the authentication controller
    fetch(`/api/check-login`, {
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
        
        return response.json();
    })
    .then((data) => {        
        if (!data.success) {
            window.location.href = `/login`;
            return;
        }

        console.debug('User is logged in:', data.email);
        
        // User is logged in, update UI elements
        updateUserInfo(data);
    })
    .catch((error) => {
        console.error('Error checking login status:', error);
        // Only redirect if we're not already on the login page
        if (!window.location.pathname.includes('login')) {
            // Ensure the redirection uses BASE_URL
            window.location.href = `/login`;
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