function fetchAccounts(accountType) {
  console.log(`Fetching accounts of type: ${accountType}`);
  fetch(`/api/get-all-${accountType}s`, {
    credentials: "same-origin",
  })
    .then((response) => response.json())
    .then((data) => {
      const userListElement = document.getElementById(`${accountType}s-list`);

      if (!data.success) {
        userListElement.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle"></i>
                        <p>Error loading ${accountType}s: ${
          data.error || "Unknown error"
        }</p>
                    </div>
                `;
        return;
      }

      if (!data.users || data.users.length === 0) {
        userListElement.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <p>No ${accountType}s found.</p>
                        <small>Create your first ${accountType} using the button above.</small>
                    </div>
                `;
        return;
      }
      let listElement = "";
      for (let user of data.users) {
        // Determine type-specific classes and icons
        let typeClass = "";
        let idAttr = "";
        if (accountType === "customer") {
          typeClass = "customer-item";
          idAttr = `data-customer-id="${user.id}"`;
        } else if (accountType === "employee") {
          typeClass = "employee-item";
          idAttr = `data-employee-id="${user.id}"`;
        } else {
          typeClass = "admin-item";
        }

        listElement += `
                        <div id="${accountType}-${user.id}" class="user-item ${typeClass} test-item p-0 pb-3" ${idAttr} style="cursor: pointer;">
                            <button class="test-button accordion-color rounded d-flex align-items-center justify-content-start text-start w-100" type="button">
                                <div>
                                <div class="fw-bold"><i class="bi bi-person-badge me-2"></i>${user.email}</div>
                                <small class="text-muted"><strong>ID: </strong>${user.id}</small>
                                </div>
                                <span class="ms-auto"><i class="bi bi-arrow-right-circle fs-4 text-primary"></i></span>
                            </button>
                        </div>
  `;
      }
      userListElement.innerHTML = listElement;

      // Add click event listeners to the newly created elements
      if (accountType === "customer") {
        const customerItems =
          userListElement.querySelectorAll(".customer-item");
        customerItems.forEach((item) => {
          item.addEventListener("click", function (e) {
            e.preventDefault();
            const customerId = this.getAttribute("data-customer-id");
            if (customerId) {
              window.location.href = `/admin/customer?id=${customerId}`;
            }
          });
        });
        console.log(
          `Added click listeners to ${customerItems.length} customer items`
        );
      } else if (accountType === "employee") {
        const employeeItems =
          userListElement.querySelectorAll(".employee-item");
        employeeItems.forEach((item) => {
          item.addEventListener("click", function (e) {
            e.preventDefault();
            const employeeId = this.getAttribute("data-employee-id");
            if (employeeId) {
              window.location.href = `/admin/pentester?id=${employeeId}`;
            }
          });
        });
      }

      console.log(`Loaded ${data.users.length} ${accountType}s successfully`);
    })
    .catch((error) => {
      document.getElementById(`${accountType}s-list`).innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Error: ${error.message}</p>
                </div>
            `;
    });
}

fetchAccounts("customer");
fetchAccounts("employee");
fetchAccounts("admin");

// Modal functions
function openAccountModal() {
  console.log("Opening account creation modal");
  const modal = document.getElementById("emailModal");
  const emailInput = document.getElementById("emailInput");
  const accountTypeSelect = document.getElementById("accountType");

  if (!modal || !emailInput || !accountTypeSelect) {
    console.error("Modal elements not found");
    return;
  }

  emailInput.value = "";
  accountTypeSelect.value = "";

  // Use Bootstrap 5 modal API
  const bootstrapModal = new bootstrap.Modal(modal);
  bootstrapModal.show();

  // Focus on account type select after modal is shown
  modal.addEventListener(
    "shown.bs.modal",
    function () {
      accountTypeSelect.focus();
    },
    { once: true }
  );

  // Remove any existing event listeners
  const form = document.getElementById("emailForm");
  const newForm = form.cloneNode(true);
  form.parentNode.replaceChild(newForm, form);

  // Add new event listener
  newForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const email = document.getElementById("emailInput").value.trim();
    const accountType = document.getElementById("accountType").value;

    if (email && accountType) {
      createAccount(accountType, email);
      bootstrapModal.hide();
    } else {
      alert("Please fill in both email and account type");
    }
  });
}

function createAccount(accountType, email) {
  console.log("Creating account:", accountType, email);

  fetch("/create-account", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      accountType: accountType,
      email: email,
    }),
    credentials: "same-origin",
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert(
          `${
            accountType.charAt(0).toUpperCase() + accountType.slice(1)
          } created successfully!`
        );

        // Refresh the appropriate list
        if (accountType === "customer") {
          fetchAccounts("customer");
        } else if (accountType === "employee") {
          fetchAccounts("employee");
        } else if (accountType === "admin") {
          fetchAccounts("admin");
        }
      } else {
        alert(
          `Error creating ${accountType}: ` + (data.error || "Unknown error")
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert(`Error creating ${accountType}: ` + error.message);
    });
}

// Function to open customer management page
function openCustomerManagement(customerId) {
  console.log("Opening customer management for ID:", customerId);
  console.log("Navigating to:", `/admin/customer?id=${customerId}`);
  window.location.href = `/admin/customer?id=${customerId}`;
}

// Make sure function is available globally
window.openCustomerManagement = openCustomerManagement;

// Debug: log when script loads
console.log("AdminDashboard.js loaded successfully");
console.log(
  "openCustomerManagement function available:",
  typeof window.openCustomerManagement
);

// Test function for debugging
function testCustomerManagement() {
  console.log("Test button clicked");
  openCustomerManagement(1);
}
window.testCustomerManagement = testCustomerManagement;

// Global click handler as ultimate backup
document.addEventListener("click", function (e) {
  console.log("Document click detected on:", e.target);
  const customerItem = e.target.closest(".customer-item");
  const employeeItem = e.target.closest(".employee-item");

  if (customerItem) {
    console.log("Click was on customer item");
    const customerId = customerItem.getAttribute("data-customer-id");
    if (customerId) {
      console.log("Global click handler - navigating to customer:", customerId);
      e.preventDefault();
      e.stopPropagation();
      window.openCustomerManagement(customerId);
    }
  } else if (employeeItem) {
    console.log("Click was on employee item");
    const employeeId = employeeItem.getAttribute("data-employee-id");
    if (employeeId) {
      console.log(
        "Global click handler - navigating to pentester:",
        employeeId
      );
      e.preventDefault();
      e.stopPropagation();
      window.openPentesterManagement(employeeId);
    } else {
      console.log("Employee item found but no employee ID attribute");
    }
  } else {
    console.log("Click was not on a customer or employee item");
  }
});

// Function to open pentester management page
function openPentesterManagement(pentesterId) {
  console.log("Opening pentester management for ID:", pentesterId);
  if (!pentesterId) {
    console.error("Pentester ID is required");
    alert("Pentester ID is required");
    return;
  }

  try {
    const url = `/admin/pentester?id=${pentesterId}`;
    console.log("Navigating to:", url);
    console.log("About to set window.location.href...");
    window.location.href = url;
    console.log("window.location.href set successfully");
  } catch (error) {
    console.error("Error navigating to pentester management:", error);
    alert("Error opening pentester management: " + error.message);
  }
}
window.openPentesterManagement = openPentesterManagement;

// Button event listeners
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM loaded, setting up button listeners");

  // Single create account button
  const createAccountBtn = document.getElementById("create-account-btn");

  console.log("Create account button found:", !!createAccountBtn);

  if (createAccountBtn) {
    createAccountBtn.addEventListener("click", () => {
      console.log("Create Account button clicked");
      openAccountModal();
    });
  } else {
    console.error("Create account button not found!");
  }
});

// Hides shadow when modal closes
const emailModal = document.getElementById("emailModal");
emailModal.addEventListener("hidden.bs.modal", () => {
  document.body.classList.remove("modal-open");
  document.querySelectorAll(".modal-backdrop").forEach((el) => el.remove());
});
