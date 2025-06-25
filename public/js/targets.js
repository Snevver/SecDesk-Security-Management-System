// Get the URL parameters
const urlParams = new URLSearchParams(window.location.search);
const test_id = urlParams.get("test_id");
const target_id = urlParams.get("target_id");

function fetchTestTargets(test_id) {
  console.log(`Fetching targets for test ID: ${test_id}`);
  fetch(`/api/get-all-targets?test_id=${test_id}`)
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      // Get both target-list elements
      const desktopList = document.getElementById("targetAccordionDesktop");
      const mobileList = document.getElementById("targetAccordionMobile");

      if (!data || !data.targets || data.targets.length === 0) {
        desktopList.innerHTML = "<p>No targets found.</p>";
        mobileList.innerHTML = "<p>No targets found.</p>";
        return;
      }

      let targetList = "";
      for (let target of data.targets) {
        targetList += `
    <div class="accordion-item">
      <h2 class="accordion-header"
            data-bs-toggle="tooltip"
            data-bs-custom-class="custom-tooltip"
            data-bs-placement="right"
            title="${target.target_description}" id="heading-${target.id}">
        <button class="accordion-button button-size collapsed p-0 pe-2 ps-1"
                type="button"
                id="target-${target.id}"
                data-bs-toggle="collapse"
                data-bs-target="#collapse-${target.id}"
                aria-expanded="false"
                aria-controls="collapse-${target.id}">
          <span class="ps-1">T${target.id}: ${target.target_name}</span>
        </button>
      </h2>
      <div id="collapse-${target.id}"
          class="accordion-collapse collapse"
          aria-labelledby="heading-${target.id}"
          data-bs-parent="#targetAccordionDesktop">
        <div class="accordion-body p-0 vulnerability-list" id="vulns-for-${target.id}">
        <div class="d-flex align-items-center">
          <p role="status">Loading...</p>
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

function fetchVulnerabilities(target_id, listId) {
    console.log(`Fetching vulnerabilities for target ID: ${target_id}`);
    fetch(`/api/get-all-vulnerabilities?target_id=${target_id}`)
        .then((response) => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then((data) => {
            // Find the correct vulnerability list element in the correct sidebar
            const vulnListSelector = `#vulns-for-${target_id}`;
            const vulnerabilityListElement = document.querySelector(
                `#${listId} ${vulnListSelector}`,
            );
            if (
                !data ||
                !data.vulnerabilities ||
                data.vulnerabilities.length === 0
            ) {
                vulnerabilityListElement.innerHTML = `<p class="ps-2 m-0">No vulnerabilities found.</p>`;
                return;
            }
            let vulnerabilityList = '';
            for (let vulnerability of data.vulnerabilities) {
                vulnerabilityList += `
              <button id="vulnerability-${vulnerability.id}" class="d-flex target-button vuln-button p-0 align-items-center">
                <p class="m-0 ps-2">
                  V${vulnerability.id}: ${vulnerability.affected_entity}
                </p>
              </button>`;
            }
            vulnerabilityListElement.innerHTML = vulnerabilityList;

            // Add event listeners for vulnerability clicks
            const vulnerabilityElements =
                vulnerabilityListElement.querySelectorAll(
                    "button[id^='vulnerability-']",
                );
            vulnerabilityElements.forEach((element) => {
                element.addEventListener('click', function (e) {
                    e.preventDefault();
                    vulnerabilityElements.forEach((el) =>
                        el.classList.remove('active'),
                    );
                    this.classList.add('active');
                    const vulnerabilityId = this.id.replace(
                        'vulnerability-',
                        '',
                    );
                    const vulnerability = data.vulnerabilities.find(
                        (v) => v.id == vulnerabilityId,
                    );
                    if (vulnerability) {
                        showVulnerabilityDetails(vulnerability);
                    }
                });
            });
        })
        .catch((error) => {
            console.error(
                'There was a problem with the fetch operation:',
                error,
            );
        });
}

function showVulnerabilityDetails(vulnerability) {
  console.log("Showing details for vulnerability:", vulnerability);

  const detailsElement = document.getElementsByClassName(
    "vulnerability-details"
  )[0];
  const detailsHeaderElement = document.getElementById("vulnerabilityDetails");

  const detailsHeaderHtml = `
        <h3 class="text-center w-100">V${vulnerability.id}: ${vulnerability.affected_entity}</h3>
  `;

  const detailsHtml = `
<div class="divTable">
  <div class="divTableBody">

    <div class="divTableRow">
      <div class="divTableCell"><strong>Identified Controls:</strong></div>
      <div class="divTableCell">${
        vulnerability.identified_controls || "N/A"
      }</div>
      <div class="divTableCell"><strong>CVSS Score:</strong></div>
      <div class="divTableCell">${vulnerability.cvss_score || "N/A"}</div>
    </div>

    <div class="divTableRow">
      <div class="divTableCell"><strong>Classification:</strong></div>
      <div class="divTableCell">${vulnerability.classification || "N/A"}</div>
      <div class="divTableCell"><strong>Residual Risk:</strong></div>
      <div class="divTableCell">${vulnerability.residual_risk || "N/A"}</div>
    </div>

    <div class="divTableRow">
      <div class="divTableCell"><strong>Location:</strong></div>
      <div class="divTableCell">${vulnerability.location || "N/A"}</div>
      <div class="divTableCell"><strong>Likelihood:</strong></div>
      <div class="divTableCell">${vulnerability.likelihood || "N/A"}</div>
    </div>

    <div class="divTableRow">
      <div class="divTableCell"><strong>Affected Component:</strong></div>
      <div class="divTableCell">${
        vulnerability.affected_component || "N/A"
      }</div>
      <div class="divTableCell"><strong>Remediation Difficulty:</strong></div>
      <div class="divTableCell">${
        vulnerability.remediation_difficulty || "N/A"
      }</div>
    </div>

    <div class="divTableRow">
        <div class="divTableCell"><strong>Risk Statement:</strong></div>
        <div class="divTableCell">${vulnerability.risk_statement || "N/A"}</div>
        <div class="divTableCell"><strong>Status:</strong></div>
        <div class="divTableCell">${
          vulnerability.solved ? "Solved" : "Open"
        }</div>
    </div>

    <div class="divTableRow">
        <div class="divTableCell"><strong>CVSS v3 Code:</strong></div>
        <div class="divTableCell">${vulnerability.cvssv3_code || "N/A"}</div>
        <div class="divTableCell"><strong>Identifier:</strong></div>
        <div class="divTableCell">${vulnerability.identifier || "N/A"}</div>
    </div>
  </div>
</div>



            <div class="ms-2 mt-2">
                <h3>Description</h3>
                <p>${vulnerability.vulnerabilities_description || "N/A"}</p>
            </div>

              ${
                vulnerability.reproduction_steps
                  ? `<div class="ms-2">
                    <h3>Reproduction Steps</h3>
                    <p>${vulnerability.reproduction_steps}</>
                </div>`
                  : ""
              }

            ${
              vulnerability.impact
                ? `<div class="ms-2">
                    <h3>Impact</h3>
                    <p>${vulnerability.impact}</p>
                </div>`
                : ""
            }

            <div class="ms-2">
                <h3>Recommendations</h3>
                <p>${vulnerability.recommendations || "N/A"}</p>
            </div>
              ${
                vulnerability.recommended_reading
                  ? `<div class="ms-2">
                    <h3>Recommended Reading</h3>
                    <p>${vulnerability.recommended_reading}</p>
                </div>`
                  : ""
              }

            ${
              vulnerability.response
                ? `<div class="ms-2">
                    <h3>Response</h3>
                    <p class="pb-2">${vulnerability.response}</p>
                </div>`
                : ""
            }
        </div>
    `;

  detailsElement.innerHTML = detailsHtml;
  detailsElement.scrollIntoView({ behavior: "smooth" });

  detailsHeaderElement.innerHTML = detailsHeaderHtml;
}

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

const desktopBtn = document.getElementById("desktopSidebarToggleBtn");
const desktopArrow = desktopBtn.querySelector(".arrow-icon");

desktopBtn.addEventListener("click", () => {
  desktopArrow.classList.toggle("rotated");
});

let lastActiveTargetId = null;
let lastActiveVulnId = null;

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
  }
});

const mobileSidebar = document.getElementById("targetSidebarMobile");
mobileSidebar.addEventListener("show.bs.offcanvas", syncMobileSidebarState);
