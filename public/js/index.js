// Debug logging function
function debugLog(message) {
    const debugEl = document.getElementById('debug');
    debugEl.style.display = 'block';
    debugEl.innerHTML += message + '<br>';
    console.log(message);
}
    
// Check if user is logged in
function checkLoginStatus() {
    // Add withCredentials to make sure cookies are sent
    fetch('isLoggedIn', {
        credentials: 'same-origin'
    })
        .then(response => {
        return response.json();
    })
    .then(data => {

    if (!data.success) {
        debugLog('User not logged in, redirecting to login page');
        window.location.href = 'login.html';
    } else {
        // First try to get data from the session response
        if (data.email) {
            document.getElementById('user-email').textContent = data.email;
        }

        if (data.role) {
            document.getElementById('user-role').textContent = data.role;
        }

        // Fallback to sessionStorage if needed
        if (!data.email) {
            const userEmail = sessionStorage.getItem('userEmail');
            if (userEmail) {
                document.getElementById('user-email').textContent = userEmail;
            }
        }

        if (!data.role) {
            const userRole = sessionStorage.getItem('userRole');
            if (userRole) {
                document.getElementById('user-role').textContent = userRole;
            }
        }
        
    }})
        .catch(error => {
        debugLog('Error checking login status: ' + error.message);
    });
}

// Handle logout
document.getElementById('logout-btn').addEventListener('click', function() {
    debugLog('Logging out...');

    fetch('logout')
        .then(response => response.json())
        .then(data => {
            debugLog('Logout response: ' + JSON.stringify(data));

            // Clear session storage
            sessionStorage.removeItem('userEmail');
            sessionStorage.removeItem('userRole');

            // Redirect to login page
            window.location.href = 'index.html';
        })
        .catch(error => {
            debugLog('Error during logout: ' + error.message);
            // Still redirect to login page in case of error
            window.location.href = 'index.html';
        });
});

// Check login status when page loads
window.addEventListener('DOMContentLoaded', checkLoginStatus);