// Get the URL parameters
const urlParams = new URLSearchParams(window.location.search);
const test_id = urlParams.get("test_id");
const target_id = urlParams.get("target_id");

/**
 * Fetch all targets for a given test
 * @param {int} test_id The ID of the test to fetch targets for
 */
function fetchTestTargets(test_id) {
  console.log(`Fetching targets for test ID: ${test_id}`);
  const desktopList = document.getElementById("targetAccordionDesktop");
  const mobileList = document.getElementById("targetAccordionMobile");

  if (desktopList) {
    desktopList.innerHTML = `
      <div class="d-flex justify-content-center align-items-center py-5 w-100">
      <strong>Loading targets...  </strong>
        <div class="spinner-border text-primary ms-2" role="status" aria-label="Loading"></div>
      </div>
    `;
  }

  fetch(`/api/get-all-targets?test_id=${test_id}`)
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      if (!data || !data.targets || data.targets.length === 0) {
        desktopList.innerHTML = `<p class="ms-2 mt-1 fw-semibold fs-5">No targets found.</p>`;
        mobileList.innerHTML = `<p class="ms-2 mt-1 fw-semibold fs-5">No targets found.</p>`;
        return;
      }

      let targetList = "";
      for (let target of data.targets) {
        targetList += `
                <div class="accordion-item w-100">
                  <h2 class="accordion-header"
                        data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip"
                        data-bs-placement="right"
                        title="${target.target_description}" id="heading-${target.id}">
                    <button class="accordion-button button-size collapsed p-4 pe-2 ps-1"
                            type="button"
                            id="target-${target.id}"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-${target.id}"
                            aria-expanded="false"
                            aria-controls="collapse-${target.id}">
                      <span class="ps-1 text-nowrap text-truncate">${target.target_name}</span>
                    </button>
                  </h2>
                  <div id="collapse-${target.id}"
                      class="accordion-collapse collapse"
                      aria-labelledby="heading-${target.id}"
                      data-bs-parent="#targetAccordionDesktop">
                    <div class="accordion-body p-0 vulnerability-list" id="vulns-for-${target.id}">
                    <div class="d-flex p-2 align-items-center">
                      <p role="status">Loading vulnerabilities...</p>
                      <div class="spinner-border spinner-border-sm ms-auto" aria-hidden="true"></div>
                    </div>
                    </div>
                  </div>
                </div>
    `;
      }
      // Populate both
      desktopList.innerHTML = targetList.replace(
        /#targetAccordionDesktop/g,
        "#targetAccordionDesktop"
      );
      mobileList.innerHTML = targetList.replace(
        /#targetAccordionDesktop/g,
        "#targetAccordionMobile"
      );

      // Enable tooltips for both
      [desktopList, mobileList].forEach((list) => {
        const newButtons = list.querySelectorAll('[data-bs-toggle="tooltip"]');
        newButtons.forEach(
          (el) =>
            new bootstrap.Tooltip(el, {
              delay: { show: 750, hide: 200 },
              trigger: "hover",
            })
        );
      });

      // Add event listeners for both
      addTargetListeners(desktopList);
      addTargetListeners(mobileList);
    })
    .catch((error) => {
      console.error("There was a problem with the fetch operation:", error);
    });
}

/**
 * Add click listeners to target buttons
 * @param {HTMLElement} targetListElement The target list element to add listeners to
 */
function addTargetListeners(targetListElement) {
  const targetElements = targetListElement.querySelectorAll(
    "button[id^='target-']"
  );
  targetElements.forEach((element) => {
    element.addEventListener("click", function (e) {
      e.preventDefault();
      const isExpanded = this.getAttribute("aria-expanded") === "false";
      targetElements.forEach((el) => el.classList.remove("active"));
      if (!isExpanded) {
        this.classList.add("active");
        const targetId = this.id.replace("target-", "");
        fetchVulnerabilities(targetId, targetListElement.id);
      } else {
        hideVulnerabilityDetails();
      }
    });
  });
}

/**
 * Fetch all vulnerabilities for a given target and populate the list element
 * @param {int} target_id The ID of the target to fetch vulnerabilities for
 * @param {int} listId The ID of the list element to populate with vulnerabilities
 */
function fetchVulnerabilities(target_id, listId) {
  console.log(`Fetching vulnerabilities for target ID: ${target_id}`);
  fetch(`/api/get-all-vulnerabilities?target_id=${target_id}`)
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      // Find the correct vulnerability list element in the correct sidebar
      const vulnListSelector = `#vulns-for-${target_id}`;
      const vulnerabilityListElement = document.querySelector(
        `#${listId} ${vulnListSelector}`
      );
      if (!data || !data.vulnerabilities || data.vulnerabilities.length === 0) {
        vulnerabilityListElement.innerHTML = `<p class="p-2 ps-2 fs-7 text-muted m-0">No vulnerabilities found.</p>`;
        return;
      }
      let vulnerabilityList = "";
      for (let vulnerability of data.vulnerabilities) {
        vulnerabilityList += `
              <button id="vulnerability-${vulnerability.id}" class="d-flex target-button vuln-button p-1 align-items-center">
                <p class="m-0 ps-2 text-nowrap text-truncate">
                  ${vulnerability.affected_entity}
                </p>
              </button>`;
      }
      vulnerabilityListElement.innerHTML = vulnerabilityList;

      // Add event listeners for vulnerability clicks
      const vulnerabilityElements = vulnerabilityListElement.querySelectorAll(
        "button[id^='vulnerability-']"
      );
      vulnerabilityElements.forEach((element) => {
        element.addEventListener("click", function (e) {
          e.preventDefault();
          vulnerabilityElements.forEach((el) => el.classList.remove("active"));
          this.classList.add("active");
          const vulnerabilityId = this.id.replace("vulnerability-", "");
          const vulnerability = data.vulnerabilities.find(
            (v) => v.id == vulnerabilityId
          );
          if (vulnerability) {
            showVulnerabilityDetails(vulnerability);
          }
        });
      });
    })
    .catch((error) => {
      console.error("There was a problem with the fetch operation:", error);
    });
}

/**
 * Load and display the details of a specific vulnerability
 * @param {Object} vulnerability The vulnerability object to display details for
 */
function showVulnerabilityDetails(vulnerability) {
  console.log("Showing details for vulnerability:", vulnerability);

  const detailsElement = document.getElementsByClassName(
    "vulnerability-details"
  )[0];

  const statusColor = vulnerability.solved ? "#55e5a0" : "#ffb347";
  const statusIcon = vulnerability.solved ? "✅" : "⚠️";
  const statusText = vulnerability.solved ? "Solved" : "Open";

  const detailsHtml = `
          <div class="d-flex align-items-center mb-1 mb-xl-0 gap-3 p-2 rounded w-100" style="background: ${statusColor};">
            <span>${statusIcon}</span>
              <div class="fw-bold">V${vulnerability.id}: ${
    vulnerability.affected_entity
  }</div>
              <div class="fw-bold">Status: ${statusText}</div>
          </div>

          <div class="divTable w-100 modern-table rounded shadow-sm mb-4">
            <div class="divTableBody w-100">
              <div class="divTableRow" style="background: #f8f9fa;">
                <div class="divTableCell py-2 px-2"><strong>🔒 Identified Controls:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.identified_controls || "N/A"
                }</div>
                <div class="divTableCell py-2 px-2"><strong>📊 CVSS Score:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.cvss_score || "N/A"
                }</div>
              </div>
              <div class="divTableRow">
                <div class="divTableCell py-2 px-2"><strong>🏷️ Classification:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.classification || "N/A"
                }</div>
                <div class="divTableCell py-2 px-2"><strong>🧮 Residual Risk:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.residual_risk || "N/A"
                }</div>
              </div>
              <div class="divTableRow" style="background: #f8f9fa;">
                <div class="divTableCell py-2 px-2"><strong>📍 Location:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.location || "N/A"
                }</div>
                <div class="divTableCell py-2 px-2"><strong>🎲 Likelihood:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.likelihood || "N/A"
                }</div>
              </div>
              <div class="divTableRow">
                <div class="divTableCell py-2 px-2"><strong>🧩 Affected Component:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.affected_component || "N/A"
                }</div>
                <div class="divTableCell py-2 px-2"><strong>🛠️ Remediation Difficulty:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.remediation_difficulty || "N/A"
                }</div>
              </div>
              <div class="divTableRow" style="background: #f8f9fa;">
                <div class="divTableCell py-2 px-2"><strong>📝 Risk Statement:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.risk_statement || "N/A"
                }</div>
                <div class="divTableCell py-2 px-2"><strong>🔢 CVSS v3 Code:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.cvssv3_code || "N/A"
                }</div>
              </div>
              <div class="divTableRow">
                <div class="divTableCell py-2 px-2"><strong>🆔 Identifier:</strong></div>
                <div class="divTableCell py-2 px-2">${
                  vulnerability.identifier || "N/A"
                }</div>
                <div class="divTableCell py-2 px-2"></div>
                <div class="divTableCell py-2 px-2"></div>
              </div>
            </div>
          </div>
          <div class="ms-2 mt-2 w-100">
            <h4>Description</h4>
            <p>${vulnerability.vulnerabilities_description || "N/A"}</p>
          </div>
          ${
            vulnerability.reproduction_steps
              ? `
            <div class="ms-2">
              <h4>Reproduction Steps</h4>
              <p>${vulnerability.reproduction_steps}</p>
            </div>
          `
              : ""
          }
          ${
            vulnerability.impact
              ? `
            <div class="ms-2">
              <h4>Impact</h4>
              <p>${vulnerability.impact}</p>
            </div>
          `
              : ""
          }
          <div class="ms-2">
            <h4>Recommendations</h4>
            <p>${vulnerability.recommendations || "N/A"}</p>
          </div>
          ${
            vulnerability.recommended_reading
              ? `
            <div class="ms-2">
              <h4>Recommended Reading</h4>
              <p>${vulnerability.recommended_reading}</p>
            </div>
          `
              : ""
          }
          ${
            vulnerability.response
              ? `
            <div class="ms-2">
              <h4>Response</h4>
              <p class="pb-5">${vulnerability.response}</p>
            </div>
          `
              : ""
          }
        `;

  detailsElement.innerHTML = detailsHtml;
  detailsElement.scrollIntoView({ behavior: "smooth" });
}

/**
 * Hide the vulnerability details section
 */
function hideVulnerabilityDetails() {
  const detailsElement = document.getElementsByClassName(
    "vulnerability-details"
  )[0];
  detailsElement.innerHTML = "";
}

// Fetch the test targets when the script loads
fetchTestTargets(test_id);

// Fetch the vulnerabilities of a target when a target is clicked
addEventListener("DOMContentLoaded", function (clickEvent) {
  const targetLinks = document.querySelectorAll("a[id^='target-']");
  targetLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.id.replace("target-", "");
      fetchVulnerabilities(targetId);
    });
  });
});

// Handle sidebar visibility based on screen size
function handleSidebarVisibility() {
  const sidebar = document.getElementById("targetSidebarDesktop");
  if (!sidebar) return;
  if (window.innerWidth >= 1200) {
    sidebar.classList.remove("d-none");
  } else {
    sidebar.classList.add("d-none");
  }
}

// Run on load and resize
window.addEventListener("DOMContentLoaded", handleSidebarVisibility);
window.addEventListener("resize", handleSidebarVisibility);

let lastActiveTargetId = null;
let lastActiveVulnId = null;

/**
 * Sync the sidebar state to mobile when resizing
 */
function syncSidebarStateToMobile() {
  // Find the expanded target in desktop sidebar
  const desktopAccordion = document.getElementById("targetAccordionDesktop");
  const expandedTarget = desktopAccordion.querySelector(
    ".accordion-button.active"
  );
  if (expandedTarget) {
    lastActiveTargetId = expandedTarget.id.replace("target-", "");
    // Optionally, find the selected vulnerability
    const activeVuln = desktopAccordion.querySelector(".vuln-button.active");
    if (activeVuln) {
      lastActiveVulnId = activeVuln.id.replace("vulnerability-", "");
    }
  } else {
    lastActiveTargetId = null;
    lastActiveVulnId = null;
  }
}

/**
 * Sync the mobile sidebar state when it is shown
 */
function syncMobileSidebarState() {
  if (lastActiveTargetId) {
    // Expand the same target in mobile sidebar
    const mobileAccordion = document.getElementById("targetAccordionMobile");
    const mobileTargetBtn = mobileAccordion.querySelector(
      `#target-${lastActiveTargetId}`
    );
    if (mobileTargetBtn) {
      // Expand the accordion
      mobileTargetBtn.click();
      // After vulnerabilities are loaded, select the same vulnerability if any
      if (lastActiveVulnId) {
        setTimeout(() => {
          const mobileVulnBtn = mobileAccordion.querySelector(
            `#vulnerability-${lastActiveVulnId}`
          );
          if (mobileVulnBtn) mobileVulnBtn.click();
        }, 300); // Adjust delay as needed for fetch timing
      }
    }
  }
}

window.addEventListener("resize", () => {
  if (window.innerWidth < 1200) {
    syncSidebarStateToMobile();
  } else {
    // If offcanvas is open, hide it and remove the backdrop
    const mobileSidebar = document.getElementById("targetSidebarMobile");
    if (mobileSidebar && mobileSidebar.classList.contains("show")) {
      // Hide the offcanvas
      const offcanvasInstance = bootstrap.Offcanvas.getInstance(mobileSidebar);
      if (offcanvasInstance) offcanvasInstance.hide();
    }
    // Remove any lingering backdrop and modal-open class
    document.body.classList.remove("offcanvas-backdrop", "modal-open");
    const backdrop = document.querySelector(".offcanvas-backdrop");
    if (backdrop) backdrop.remove();
  }
});

const mobileSidebar = document.getElementById("targetSidebarMobile");
mobileSidebar.addEventListener("show.bs.offcanvas", syncMobileSidebarState);

fetch(`/api/get-test`, {
  method: "POST",
  credentials: "same-origin",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    test_id: test_id,
  }),
})
  .then((response) => {
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.json();
  })
  .then((data) => {
    const detailsHeaderElement = document.getElementById(
      "vulnerabilityDetailsHeader"
    );

    if (detailsHeaderElement) {
      detailsHeaderElement.innerHTML = `${data.test_name}`;
    }
  })
  .catch((error) => {
    console.error("Error fetching initial test data:", error);
    // Set fallback values
    if (detailsHeaderElement) {
      detailsHeaderElement.textContent = "Error loading title";
    }
  });

const icon = document.getElementById("icon");
const icon1 = document.getElementById("a");
const icon2 = document.getElementById("b");
const icon3 = document.getElementById("c");
const nav = document.getElementById("nav");
const blue = document.getElementById("blue");

icon.addEventListener("click", () => {
  icon1.classList.toggle("a");
  icon2.classList.toggle("c");
  icon3.classList.toggle("b");
  nav.classList.toggle("show");
  blue.classList.toggle("slide");
});
