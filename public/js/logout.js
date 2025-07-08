document.addEventListener("DOMContentLoaded", () => {
    const logoutBtn = document.getElementById("logout-btn");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
            // Disable the button to prevent multiple clicks
            logoutBtn.disabled = true;
            logoutBtn.textContent = "Logging out...";

            fetch(`/api/logout`, {
                method: "GET",
                credentials: "same-origin", // Include cookies in the request
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(
                            `HTTP error! status: ${response.status}`
                        );
                    }
                    return response.json();
                })
                .then((data) => {
                    console.log("Logout successful:", data);
                    // Clear any client-side storage if needed
                    localStorage.clear();
                    sessionStorage.clear();
                    // Redirect to login page
                    window.location.href = `/login`;
                })
                .catch((error) => {
                    console.error("Logout error:", error);
                    // Even if there's an error, redirect to login for security
                    window.location.href = `/login`;
                });
        });
    } else {
        console.error("Logout button not found in the DOM");
    }
});
