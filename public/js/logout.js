// Set up logout handler
function setupLogout() {
    const logoutBtn = document.getElementById('logout-btn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(event) {

            fetch(`/api/logout`)
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    console.debug("Logout data:", data);

                    // Redirect to login page
                    window.location.href = `/login`;
                })
                .catch((error) => {
                    console.error('Logout error:', error);
                    window.location.href = `/login`;
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