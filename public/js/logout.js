//======================================================================
// HANDLE LOG OUT
//======================================================================

document.getElementById("logout-btn").addEventListener("click", () => {
    fetch(`/api/logout`)
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            // Redirect to login page
            window.location.href = `/login`;
        })
        .catch((error) => {
            console.error("Logout error:", error);
            window.location.href = `/login`;
        });
});
