// Set up logout handler
function setupLogout() {
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            fetch('logout')
                .then((response) => response.json())
                .then((data) => {
                    // Clear session storage
                    sessionStorage.removeItem('userEmail');
                    sessionStorage.removeItem('userRole');
                    sessionStorage.removeItem('userId');

                    // Redirect to login page
                    window.location.href = 'login.html';
                })
                .catch((error) => {
                    // Still redirect to login page in case of error
                    window.location.href = 'login.html';
                });
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    setupLogout();
});
