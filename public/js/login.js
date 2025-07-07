//======================================================================
// LOGIN PAGE SCRIPT
//======================================================================

/**
 * Print error message on DOM
 * @param {string} message The message to be displayed
 */
function showError(message) {
  document.getElementById("error-message").innerHTML = `
    <div class="alert alert-danger alert-dismissible fade show text-nowrap m-0" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;
}

document.getElementById("login-form").addEventListener("submit", (event) => {
  event.preventDefault();

  // Clear previous messages
  const debugElement = document.getElementById("debug");
  const errorElement = document.getElementById("error-message");
  if (debugElement) debugElement.innerHTML = "";
  if (errorElement) errorElement.textContent = "";

  const loginBtn = document.getElementById("login");
  const spinner = document.getElementById("loginSpinner");
  const btnText = document.getElementById("loginBtnText");
  if (loginBtn && spinner && btnText) {
    spinner.classList.remove("d-none");
    btnText.textContent = "Logging in...";
  }

  // Get form values
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;

  // Use base URL with path
  fetch(`/api/login`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify({
      email: email,
      password: password,
    }),
    credentials: "include",
  })
    .then((rawResponse) => rawResponse.json())
    .then((response) => {
      // Check for redirects - If the login was successful, it should go here
      if (response.redirect) {
        window.location.href = response.redirect;
        // return Promise.reject(new Error("Redirect"));
      } else if (response.error) {
        // Login failed, show error and reset button
        showError(response.error);
        resetLoginButton();
      }
    })
    .catch((error) => {
      // Only show error if not a redirect
      if (error.message !== "Redirected") {
        showError(error.message || "An error occurred. Please try again.");
        resetLoginButton();
      }
    });
});

function setEqualHeight(selector1, selector2) {
  const el1 = document.querySelector(selector1);
  const el2 = document.querySelector(selector2);
  if (!el1 || !el2) return;

  // Reset heights to auto to recalculate
  el1.style.height = "auto";
  el2.style.height = "auto";

  // Get the tallest height
  const h1 = el1.offsetHeight;
  const h2 = el2.offsetHeight;
  console.log(h1, h2);
  const maxHeight = Math.max(h1, h2);

  // Set both to the tallest
  el1.style.height = maxHeight + "px";
  el2.style.height = maxHeight + "px";
}

// Run on load and resize
function equalizeLoginAndDesc() {
  setEqualHeight("#loginSection", "#homeContent");
}

document.addEventListener("DOMContentLoaded", equalizeLoginAndDesc);
window.addEventListener("resize", equalizeLoginAndDesc);

function resetLoginButton() {
  const loginBtn = document.getElementById("login");
  const spinner = document.getElementById("loginSpinner");
  const btnText = document.getElementById("loginBtnText");
  if (loginBtn && spinner && btnText) {
    spinner.classList.add("d-none");
    btnText.textContent = "Login";
  }
}
