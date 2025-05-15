//======================================================================
// EMPLOYEE DASHBOARD SCRIPT
//======================================================================

/**
 * Fetch and display customer data
 */
function fetchCustomers() {
    fetch(`/api/customers`, {
        credentials: "same-origin",
    })
        .then((response) => response.json())
        .then((data) => {
            const userListElement = document.getElementById("userList");

            if (!data.success) {
                userListElement.innerHTML =
                    "<p>Error loading users: " +
                    (data.error || "Unknown error") +
                    "</p>";
                return;
            }

            if (!data.users || data.users.length === 0) {
                userListElement.innerHTML = "<p>No users found.</p>";
                return;
            }

            let listElement = "<ul>";
            data.users.forEach((user) => {
                listElement += `<li>Email: ${user.email} <br> ID: ${user.id}</li>`;
            });
            listElement += "</ul>";

            userListElement.innerHTML = listElement;
        })
        .catch((error) => {
            document.getElementById("userList").innerHTML =
                "<p>Error: " + error.message + "</p>";
        });
}

/**
 * Fetch and display user email and role
 */
function fetchUserInfo() {
    fetch(`/api/check-login`, {
        credentials: "same-origin",
    })
        .then((response) => response.json())
        .then((data) => {
            const emailElement = document.getElementById("userEmail");
            const roleElement = document.getElementById("userRole");

            if (!data.success) {
                emailElement.textContent = "Unknown";
                roleElement.textContent = "Unknown";
                return;
            }

            emailElement.textContent = data.user.email || "Unknown";
            roleElement.textContent = data.user.role || "Unknown";
        })
        .catch(() => {
            document.getElementById("userEmail").textContent = "Unknown";
            document.getElementById("userRole").textContent = "Unknown";
        });
}

fetchUserInfo();
fetchCustomers();
