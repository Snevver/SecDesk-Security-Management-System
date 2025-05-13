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

// Check login when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    checkLoginStatus();
});