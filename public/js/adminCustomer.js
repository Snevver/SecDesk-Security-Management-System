// Get customer ID from URL parameters
const urlParams = new URLSearchParams(window.location.search);
const customerId = urlParams.get("id");

if (!customerId) {
  alert("Customer ID not provided");
  window.location.href = "/";
}

// Filter state management
let currentTestFilter = null;
let currentTargetFilter = null;
let allTargets = [];
let allVulnerabilities = [];

// Load customer data on page load
document.addEventListener("DOMContentLoaded", function () {
  loadCustomerDetails();
  loadCustomerTests();
  loadCustomerTargets();
  loadCustomerVulnerabilities();
});

// Tab event listeners
document.getElementById("targets-tab").addEventListener("click", function () {
  if (!allTargets.length) {
    loadCustomerTargets();
  } else {
    displayTargets(allTargets);
  }
});

document
  .getElementById("vulnerabilities-tab")
  .addEventListener("click", function () {
    if (!allVulnerabilities.length) {
      loadCustomerVulnerabilities();
    } else {
      displayVulnerabilities(allVulnerabilities);
    }
  });

// Load customer details
async function loadCustomerDetails() {
  try {
    const response = await fetch(
      `/api/customer-details?customer_id=${customerId}`
    );
    const data = await response.json();
    if (data.success) {
      const customer = data.customer;
      document.getElementById("customer-name").textContent = customer.email;
      document.getElementById("customer-email").textContent = customer.email;
      document.getElementById("customer-id").textContent = customer.id;
      document.getElementById("customer-joined").textContent = new Date(
        customer.creation_date
      ).toLocaleDateString();
    } else {
      console.error("Failed to load customer details:", data.error);
      alert("Failed to load customer details: " + data.error);
    }
  } catch (error) {
    console.error("Error loading customer details:", error);
    alert("Error loading customer details");
  }
}

// Load customer tests
async function loadCustomerTests() {
  try {
    const response = await fetch(
      `/api/customer-tests?customer_id=${customerId}`
    );
    const data = await response.json();

    if (data.success) {
      const tests = data.tests;
      document.getElementById("total-tests").textContent = tests.length;

      const tbody = document.getElementById("tests-table-body");
      if (tests.length === 0) {
        tbody.innerHTML =
          '<tr><td colspan="7" class="text-center text-muted">No tests found</td></tr>';
      } else {
        tbody.innerHTML = tests
          .map(
            (test) => `
                    <tr>
                        <td>${test.id}</td>
                        <td><a href="#" class="text-decoration-none fw-bold clickable-link" onclick="filterTargetsByTest(${
                          test.id
                        }, '${escapeHtml(
              test.test_name || "Untitled"
            )}')" title="Click to view targets for this test">${escapeHtml(
              test.test_name || "Untitled"
            )}</a></td>
                        <td>${escapeHtml(
                          test.test_description || "No description"
                        )}</td>
                        <td><span class="badge bg-${
                          test.completed ? "success" : "warning"
                        }">${
              test.completed ? "Completed" : "In Progress"
            }</span></td>
                        <td>${escapeHtml(
                          test.pentester_email || "Not assigned"
                        )}</td>
                        <td>${
                          test.test_date
                            ? new Date(test.test_date).toLocaleDateString()
                            : "Not set"
                        }</td>
                    </tr>
                `
          )
          .join("");
      }
    } else {
      console.error("Failed to load tests:", data.error);
    }
  } catch (error) {
    console.error("Error loading tests:", error);
  }
}

// Load customer targets
async function loadCustomerTargets() {
  try {
    const response = await fetch(
      `/api/customer-targets?customer_id=${customerId}`
    );
    const data = await response.json();

    if (data.success) {
      allTargets = data.targets;
      displayTargets(allTargets);
    } else {
      console.error("Failed to load targets:", data.error);
    }
  } catch (error) {
    console.error("Error loading targets:", error);
  }
}

// Display targets with optional filtering
function displayTargets(targets) {
  document.getElementById("total-targets").textContent = targets.length;

  const tbody = document.getElementById("targets-table-body");
  if (targets.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="5" class="text-center text-muted">No targets found</td></tr>';
  } else {
    tbody.innerHTML = targets
      .map(
        (target) => `
            <tr>
                <td>${target.id}</td>
                <td>${escapeHtml(target.test_name || "Unknown")}</td>
                <td><a href="#" class="text-decoration-none fw-bold clickable-link" onclick="filterVulnerabilitiesByTarget(${
                  target.id
                }, '${escapeHtml(
          target.target_name || "Untitled"
        )}')" title="Click to view vulnerabilities for this target">${escapeHtml(
          target.target_name || "Untitled"
        )}</a></td>
                <td>${escapeHtml(
                  target.target_description || "No description"
                )}</td>
            </tr>
        `
      )
      .join("");
  }
}

// Load customer vulnerabilities
async function loadCustomerVulnerabilities() {
  try {
    const response = await fetch(
      `/api/customer-vulnerabilities?customer_id=${customerId}`
    );
    const data = await response.json();

    if (data.success) {
      allVulnerabilities = data.vulnerabilities;
      displayVulnerabilities(allVulnerabilities);
    } else {
      console.error("Failed to load vulnerabilities:", data.error);
    }
  } catch (error) {
    console.error("Error loading vulnerabilities:", error);
  }
}

// Display vulnerabilities with optional filtering
function displayVulnerabilities(vulnerabilities) {
  document.getElementById("total-vulnerabilities").textContent =
    vulnerabilities.length;

  const container = document.getElementById("vulnerabilities-container");
  if (vulnerabilities.length === 0) {
    container.innerHTML =
      '<div class="text-center p-4 text-muted">No vulnerabilities found</div>';
  } else {
    container.innerHTML = vulnerabilities
      .map((vuln) => createVulnerabilityCard(vuln))
      .join("");
  }
}

function createVulnerabilityCard(vuln) {
  const cvssClass = getCvssClass(vuln.cvss_score);
  const statusClass = vuln.solved ? "solved" : "open";
  const cardId = `vuln-${vuln.id}`;

  return `
        <div class="vulnerability-card mb-4">
            <div class="accordion accordion-flush" id="accordion-${cardId}">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed d-flex align-items-center p-2 rounded"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse-${cardId}"
                                aria-expanded="false"
                                aria-controls="collapse-${cardId}"
                                style="background: var(--light-background-color)">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <strong>ID: ${
                                      vuln.id
                                    }</strong> - ${escapeHtml(
    vuln.affected_entity || "Unknown Entity"
  )}
                                </h6>
                                <small class="text-muted">
                                    Test: ${escapeHtml(
                                      vuln.test_name || "Unknown"
                                    )} |
                                    Target: ${escapeHtml(
                                      vuln.target_name || "Unknown"
                                    )}
                                </small>
                            </div>
                            <div class="ms-auto d-flex align-items-center gap-2 me-3">
                                <span class="status-badge status-${statusClass}"><strong>Status: </strong>${
    vuln.solved ? "✅ Solved" : "⚠️ Open"
  }</span>
                            </div>
                        </button>
                    </h2>

                    <div id="collapse-${cardId}" class="accordion-collapse collapse" data-bs-parent="#accordion-${cardId}">
                        <div class="accordion-body p-3">
                            <!-- Main Info Table -->
                            <div class="divTable modern-table rounded shadow-sm mb-2">
                                <div class="divTableBody">
                    <div class="divTableRow">
                        <div class="divTableCell py-2 px-2"><strong>Identifier:</strong></div>
                        <div class="divTableCell py-2 px-2">${escapeHtml(
                          vuln.identifier || "Not specified"
                        )}</div>
                        <div class="divTableCell py-2 px-2"><strong>Classification:</strong></div>
                        <div class="divTableCell py-2 px-2">${escapeHtml(
                          vuln.classification || "Not specified"
                        )}</div>
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell py-2 px-2"><strong>Controls:</strong></div>
                        <div class="divTableCell py-2 px-2">${escapeHtml(
                          vuln.identified_controls || "Not specified"
                        )}</div>
                        <div class="divTableCell py-2 px-2"><strong>CVSS Score:</strong></div>
                        <div class="divTableCell py-2 px-2">${
                          vuln.cvss_score || "N/A"
                        }</div>
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell py-2 px-2"><strong>Location:</strong></div>
                        <div class="divTableCell py-2 px-2">${escapeHtml(
                          vuln.location || "Not specified"
                        )}</div>
                        <div class="divTableCell py-2 px-2"><strong>Affected Component:</strong></div>
                        <div class="divTableCell py-2 px-2">${escapeHtml(
                          vuln.affected_component || "Not specified"
                        )}</div>
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell py-2 px-2"><strong>Risk Statement:</strong></div>
                        <div class="divTableCell py-2 px-2">${escapeHtml(
                          vuln.risk_statement || "Not provided"
                        )}</div>
                        <div class="divTableCell py-2 px-2"><strong>Likelihood:</strong></div>
                        <div class="divTableCell py-2 px-2">${escapeHtml(
                          vuln.likelihood || "Not assessed"
                        )}</div>
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell py-2 px-2"><strong>Residual Risk:</strong></div>
                        <div class="divTableCell py-2 px-2">${escapeHtml(
                          vuln.residual_risk || "Not assessed"
                        )}</div>
                        <div class="divTableCell py-2 px-2"><strong>Difficulty:</strong></div>
                        <div class="divTableCell py-2 px-2">${escapeHtml(
                          vuln.remediation_difficulty || "Not assessed"
                        )}</div>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div class="mb-3">
                <div class="mb-2">
                    <strong>Description:</strong><br>
                    <p class="mb-1 ps-2">${escapeHtml(
                      vuln.vulnerabilities_description ||
                        "No description provided"
                    )}</p>
                </div>

                <div class="mb-2">
                    <strong>Reproduction Steps:</strong><br>
                    <pre class="bg-light p-2 rounded mb-1" style="white-space: pre-wrap; font-size: 0.9rem;">${escapeHtml(
                      vuln.reproduction_steps || "No steps provided"
                    )}</pre>
                </div>

                <div class="mb-2">
                    <strong>Impact:</strong><br>
                    <p class="mb-1 ps-2">${escapeHtml(
                      vuln.impact || "Not assessed"
                    )}</p>
                </div>

                <div class="mb-2">
                    <strong>Recommendations:</strong><br>
                    <p class="mb-1 ps-2">${escapeHtml(
                      vuln.recommendations || "No recommendations provided"
                    )}</p>
                </div>

                <div class="mb-2">
                    <strong>Recommended Reading:</strong><br>
                    <p class="mb-1 ps-2">${escapeHtml(
                      vuln.recommended_reading || "None provided"
                    )}</p>
                </div>
            </div>

                          <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center text-muted border-top pt-2">
                                <small>Created: ${
                                  vuln.created_at
                                    ? new Date(vuln.created_at).toLocaleString()
                                    : "Unknown"
                                }</small>
                                <div class="text-end pe-2">
                                    <small>CVSSv3 Vector: <code>${escapeHtml(
                                      vuln.cvssv3_code || "Not provided"
                                    )}</code></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Get CVSS class for styling
function getCvssClass(cvssScore) {
  if (!cvssScore) return "low";
  const score = parseFloat(cvssScore);
  if (score >= 9.0) return "critical";
  if (score >= 7.0) return "high";
  if (score >= 4.0) return "medium";
  return "low";
}

// Toggle vulnerability details
function toggleVulnerabilityDetails(vulnId) {
  const details = document.getElementById(`vuln-details-${vulnId}`);
  const indicator = details
    .closest(".vulnerability-card")
    .querySelector(".expand-indicator");

  if (details.classList.contains("show")) {
    details.classList.remove("show");
    indicator.classList.remove("expanded");
  } else {
    details.classList.add("show");
    indicator.classList.add("expanded");
  }
}

// Filter functions
function filterTargetsByTest(testId, testName) {
  currentTestFilter = testId;
  const filteredTargets = allTargets.filter(
    (target) => target.test_id == testId
  );

  // Switch to targets tab using Bootstrap API
  const targetsTab = new bootstrap.Tab(document.getElementById("targets-tab"));
  targetsTab.show();

  // Update header to show filter status
  setTimeout(() => {
    const targetHeader = document.querySelector("#targets .stats-card h4");
    targetHeader.innerHTML = `Customer Targets - Filtered by Test: ${testName} <button class="btn btn-sm btn-outline-secondary ms-2 text-white" onclick="clearTargetsFilter()">Clear Filter</button>`;
    displayTargets(filteredTargets);
  }, 100);
}

function filterVulnerabilitiesByTarget(targetId, targetName) {
  currentTargetFilter = targetId;
  const filteredVulnerabilities = allVulnerabilities.filter(
    (vuln) => vuln.target_id == targetId
  );

  // Switch to vulnerabilities tab using Bootstrap API
  const vulnTab = new bootstrap.Tab(
    document.getElementById("vulnerabilities-tab")
  );
  vulnTab.show();
  // Update header to show filter status
  setTimeout(() => {
    const vulnHeader = document.querySelector(
      "#vulnerabilities .stats-card h4"
    );
    vulnHeader.innerHTML = `Customer Vulnerabilities - Filtered by Target: ${targetName} <button class="btn btn-sm btn-outline-secondary ms-2 text-white" onclick="clearVulnerabilitiesFilter()">Clear Filter</button>`;
    displayVulnerabilities(filteredVulnerabilities);
  }, 100);
}

function clearTargetsFilter() {
  currentTestFilter = null;
  const targetHeader = document.querySelector("#targets .stats-card h4");
  targetHeader.innerHTML = "Customer Targets";
  displayTargets(allTargets);
}

function clearVulnerabilitiesFilter() {
  currentTargetFilter = null;
  const vulnHeader = document.querySelector("#vulnerabilities .stats-card h4");
  vulnHeader.innerHTML = "Customer Vulnerabilities";
  displayVulnerabilities(allVulnerabilities);
}

// Delete customer function
function deleteCustomer() {
  if (
    confirm(
      "Are you sure you want to delete this customer? This action cannot be undone and will delete all associated tests, targets, and vulnerabilities."
    )
  ) {
    fetch(`/api/delete?user_id=${customerId}`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Customer deleted successfully");
          window.location.href = "/";
        } else {
          alert("Error deleting customer: " + (data.error || "Unknown error"));
        }
      })
      .catch((error) => {
        console.error("Error deleting customer:", error);
        alert("Error deleting customer");
      });
  }
}

// Helper functions
function escapeHtml(text) {
  if (text === null || text === undefined || text === "") {
    return "";
  }
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

function safeValue(value, defaultText = "Not provided") {
  if (
    value === null ||
    value === undefined ||
    value === "" ||
    value.trim === ""
  ) {
    return defaultText;
  }
  return escapeHtml(value);
}

function truncateText(text, maxLength) {
  return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
}

function getStatusColor(status) {
  const statusColors = {
    completed: "success",
    "in progress": "info",
    pending: "warning",
    cancelled: "secondary",
    active: "success",
    inactive: "secondary",
    vulnerable: "danger",
    secure: "success",
    open: "danger",
    fixed: "success",
    closed: "secondary",
  };
  return statusColors[status?.toLowerCase()] || "secondary";
}

function getSeverityClass(cvssScore) {
  if (!cvssScore) return "low";
  const score = parseFloat(cvssScore);
  if (score >= 9.0) return "critical";
  if (score >= 7.0) return "high";
  if (score >= 4.0) return "medium";
  return "low";
}

function deleteUser(userId) {
  if (confirm("Are you sure you want to delete this pentester?")) {
    fetch(`/api/delete?user_id=${userId}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Pentester deleted successfully");
          window.location.href = "/";
        } else {
          alert("Failed to delete pentester: " + data.error);
        }
      })
      .catch((error) => {
        console.error("Error deleting pentester:", error);
        alert("Error deleting pentester");
      });
  }
}
