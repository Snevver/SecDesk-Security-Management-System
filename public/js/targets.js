// Get the URL parameters
const urlParams = new URLSearchParams(window.location.search);
const test_id = urlParams.get("test_id");
const target_id = urlParams.get("target_id");

function fetchTestTargets(test_id) {
  console.log(`Fetching targets for test ID: ${test_id}`);
  fetch(`/api/targets?test_id=${test_id}`)
    .then((response) => {
      if (!response.ok) {
        console.log(response);
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      const targetListElement =
        document.getElementsByClassName("target-list")[0];
      if (!data || !data.targets || data.targets.length === 0) {
        targetListElement.innerHTML = "<p>No targets found.</p>";
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
          data-bs-parent="#targetAccordion">
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
      targetListElement.innerHTML = targetList;

      // Enabling Bootstrap tooltips with delay, only working on hover not focus
      const newButtons = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
      );
      newButtons.forEach(
        (el) =>
          new bootstrap.Tooltip(el, {
            delay: { show: 750, hide: 200 },
            trigger: "hover",
          })
      );

      // Add event listeners after elements are added to DOM
      const targetElements = targetListElement.querySelectorAll(
        "button[id^='target-']"
      );
      targetElements.forEach((element) => {
        element.addEventListener("click", function (e) {
          e.preventDefault();

          // Check if the button is already expanded
          const isExpanded = this.getAttribute("aria-expanded") === "false";

          // Remove 'active' class from all first
          targetElements.forEach((el) => el.classList.remove("active"));

          // Only add 'active' class if the button is being expanded
          if (!isExpanded) {
            this.classList.add("active");
            const targetId = this.id.replace("target-", "");
            console.log(`Clicking on target ${targetId}`);
            fetchVulnerabilities(targetId);
          } else {
            // If collapsing, hide vulnerability details
            hideVulnerabilityDetails();
          }
        });
      });
    })
    .catch((error) => {
      console.error("There was a problem with the fetch operation:", error);
    });
}

function fetchVulnerabilities(target_id) {
  console.log(`Fetching vulnerabilities for target ID: ${target_id}`);
  fetch(`/api/vulnerabilities?target_id=${target_id}`)
    .then((response) => {
      if (!response.ok) {
        console.log(response);
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      console.log("Vulnerabilities:", data.vulnerabilities);
      // Get element by unique ID to display vulnerabilities in the correct target element
      const vulnerabilityListElement = document.getElementById(
        `vulns-for-${target_id}`
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

      // Add event listeners for vulnerability clicks after elements are added to DOM
      const vulnerabilityElements = vulnerabilityListElement.querySelectorAll(
        "button[id^='vulnerability-']"
      );
      vulnerabilityElements.forEach((element) => {
        element.addEventListener("click", function (e) {
          e.preventDefault();

          // Remove 'focused' class from all first, then add 'focused to the clicked element
          vulnerabilityElements.forEach((el) => el.classList.remove("active"));
          this.classList.add("active");

          const vulnerabilityId = this.id.replace("vulnerability-", "");
          console.log(`Clicking on vulnerability ${vulnerabilityId}`);

          // Find the vulnerability data from the response
          const vulnerability = data.vulnerabilities.find(
            (vulnerability) => vulnerability.id == vulnerabilityId
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
