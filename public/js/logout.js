// Get base URL
const BASE_URL = window.location.origin + '/SecDesk-Security-Management-System/public';

// Set up logout handler
function setupLogout() {
    const logoutBtn = document.getElementById('logout-btn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(event) {
            // Prevent the default anchor behavior
            event.preventDefault();
            
            fetch(`${BASE_URL}/logout`)
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    console.log("Logout data:", data);
                    // Clear session storage
                    sessionStorage.removeItem('userEmail');
                    sessionStorage.removeItem('userRole');
                    sessionStorage.removeItem('userId');

                    // Redirect to login page
                    window.location.href = `${BASE_URL}/login`;
                })
                .catch((error) => {
                    console.error('Logout error:', error);
                    window.location.href = `${BASE_URL}/login`;
                });
        });
    } else {
        console.error("Logout button not found!");
    }
}

// Make sure DOM is loaded before setting up event listeners
document.addEventListener('DOMContentLoaded', function() {
    setupLogout();
});