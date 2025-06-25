// Get the URL parameters
const urlParams = new URLSearchParams(window.location.search);
const test_id = urlParams.get("test_id");
const target_id = urlParams.get("target_id");

let selectedTargetId = null;
let selectedVulnId = null;

function fetchTestTargets(test_id) {
  fetch(`/api/targets?test_id=${test_id}`)
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
      const targetId = this.id.replace("target-", "");
      selectedTargetId = targetId;
      selectedVulnId = null; // Reset vuln selection
      // Remove 'active' from all, add to this
      targetElements.forEach((el) => el.classList.remove("active"));
      this.classList.add("active");
      fetchVulnerabilities(targetId, targetListElement.id);
    });
  });
}

function fetchVulnerabilities(target_id, listId) {
  fetch(`/api/vulnerabilities?target_id=${target_id}`)
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      // Find the correct vulnerability list element in the correct sidebar
      const vulnListSelector =
        listId === "targetAccordionDesktop"
          ? `#vulns-for-${target_id}`
          : `#vulns-for-${target_id}`;
      const vulnerabilityListElement = document.querySelector(
        `#${listId} ${vulnListSelector}`
      );
      if (!data || !data.vulnerabilities || data.vulnerabilities.length === 0) {
        vulnerabilityListElement.innerHTML = `<p class="ps-2 m-0">No vulnerabilities found.</p>`;
        return;
      }
      let vulnerabilityList = "";
      for (let vulnerability of data.vulnerabilities) {
        vulnerabilityList += `
              <button id="vulnerability-${vulnerability.id}" class="d-flex target-button vuln-button p-0 align-items-center">
                <p class="m-0 ps-2">
                  V${vulnerability.id}: ${vulnerability.affected_entity}
                </p>
              </button>`;
      }
      vulnerabilityListElement.innerHTML = vulnerabilityList;

      // After rendering vulnerability buttons:
      const vulnerabilityElements = vulnerabilityListElement.querySelectorAll(
        "button[id^='vulnerability-']"
      );
      vulnerabilityElements.forEach((element) => {
        element.addEventListener("click", function (e) {
          e.preventDefault();
          vulnerabilityElements.forEach((el) => el.classList.remove("active"));
          this.classList.add("active");
          const vulnId = this.id.replace("vulnerability-", "");
          selectedVulnId = vulnId;
          const vulnerability = data.vulnerabilities.find(
            (v) => v.id == vulnId
          );
          if (vulnerability) {
            showVulnerabilityDetails(vulnerability);
          }
        });
        // If this vuln is the selected one, activate it
        if (
          selectedVulnId &&
          element.id === `vulnerability-${selectedVulnId}`
        ) {
          element.classList.add("active");
        }
      });
    })
    .catch((error) => {
      console.error("There was a problem with the fetch operation:", error);
    });
}

// The rest of your showVulnerabilityDetails and hideVulnerabilityDetails functions remain unchanged

// Fetch the test targets when the script loads
fetchTestTargets(test_id);

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

function restoreSidebarState(sidebarType) {
  const accordionId =
    sidebarType === "desktop"
      ? "targetAccordionDesktop"
      : "targetAccordionMobile";
  const accordion = document.getElementById(accordionId);

  if (selectedTargetId) {
    const targetBtn = accordion.querySelector(`#target-${selectedTargetId}`);
    if (targetBtn) {
      // Remove 'active' from all accordion buttons first
      accordion
        .querySelectorAll("button[id^='target-']")
        .forEach((btn) => btn.classList.remove("active"));

      // Only click if not already expanded
      if (targetBtn.getAttribute("aria-expanded") === "false") {
        targetBtn.click();
      } else {
        fetchVulnerabilities(selectedTargetId, accordionId);
      }
      // Add 'active' class to the selected button
      targetBtn.classList.add("active");

      // After vulnerabilities are loaded, select the vuln if any
      if (selectedVulnId) {
        setTimeout(() => {
          const vulnBtn = accordion.querySelector(
            `#vulnerability-${selectedVulnId}`
          );
          if (vulnBtn && !vulnBtn.classList.contains("active")) {
            vulnBtn.click();
          }
        }, 400);
      }
    }
  }
}

window.addEventListener("resize", () => {
  if (window.innerWidth < 1200) {
    restoreSidebarState("mobile");
  } else {
    restoreSidebarState("desktop");
  }
});

const mobileSidebar = document.getElementById("targetSidebarMobile");
if (mobileSidebar) {
  mobileSidebar.addEventListener("show.bs.offcanvas", () =>
    restoreSidebarState("mobile")
  );
}

const offcanvas = document.getElementById("targetSidebarMobile");
const offcanvasBtn = document.getElementById("mobileSidebarToggleBtn");
const arrowIcon = offcanvasBtn.querySelector(".arrow-icon");

offcanvas.addEventListener("show.bs.offcanvas", () => {
  arrowIcon.classList.add("rotated");
});
offcanvas.addEventListener("hide.bs.offcanvas", () => {
  arrowIcon.classList.remove("rotated");
});

const desktopBtn = document.getElementById("desktopSidebarToggleBtn");
const desktopArrow = desktopBtn.querySelector(".arrow-icon");

desktopBtn.addEventListener("click", () => {
  desktopArrow.classList.toggle("rotated");
});
