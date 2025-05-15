//======================================================================
// EMPLOYEE DASHBOARD SCRIPT
//======================================================================

/**
 * Fetch and customer data
 */
function fetchCustomers() {
    fetch(`/api/customers`, {
        credentials: "same-origin",
    })
        .then((response) => response.json())
        .then((data) => displayCustomers(data))
        .catch(
            (error) =>
                (document.getElementById("userList").innerHTML =
                    "<p>Error: " + error.message + "</p>")
        );
}

/**
 * Display customer data on DOM
 * @param {object} customers Customer data fetched from database
 * @returns {void}
 */
function displayCustomers(customers) {
    const userListElement = document.getElementById("userList");

    if (!customers.success) {
        userListElement.innerHTML =
            "<p>Error loading users: " +
            (customers.error || "Unknown error") +
            "</p>";
        return;
    }

    if (!customers.users || customers.users.length === 0) {
        userListElement.innerHTML = "<p>No users found.</p>";
        return;
    }

    let listElement = "<ul>";
    
    for (let user of customers.users) listElement += `<li>Email: ${user.email} <br> ID: ${user.id}</li>`;

    listElement += "</ul>";

    userListElement.innerHTML = listElement;
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
