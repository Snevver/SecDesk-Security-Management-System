// Get the URL parameters
const urlParams = new URLSearchParams(window.location.search);
const test_id = urlParams.get('test_id');
const target_id = urlParams.get('target_id');

function fetchTestTargets(test_id) {
  console.log(`Fetching targets for test ID: ${test_id}`);
  fetch(`/api/get-all-targets?test_id=${test_id}`)
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

            targetListElement.innerHTML = targetList;

            // Add event listeners after elements are added to DOM
            const targetElements =
                targetListElement.querySelectorAll("div[id^='target-']");
            targetElements.forEach((element) => {
                element.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.id.replace('target-', '');
                    console.log(`Clicking on target ${targetId}`);
                    fetchVulnerabilities(targetId);
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

function fetchVulnerabilities(target_id) {
  console.log(`Fetching vulnerabilities for target ID: ${target_id}`);
  fetch(`/api/get-all-vulnerabilities?target_id=${target_id}`)
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

            let vulnerabilityList = '';
            for (let vulnerability of data.vulnerabilities) {
                vulnerabilityList += `
                    <div id="vulnerability-${vulnerability.id}">
                        <h3>
                            ${vulnerability.affected_entity}
                        </h3>
                        <div>
                            <p><strong>Classification:</strong>
                                ${vulnerability.classification}
                            </p>
                            <p><strong>CVSS Score:</strong>
                                ${vulnerability.cvss_score}
                            </p>
                            <p><strong>Risk:</strong>
                                ${vulnerability.likelihood}
                            </p>
                            <p>${vulnerability.vulnerabilities_description}</p>
                        </div>
                    </div>`;
            }

            vulnerabilityListElement.innerHTML = vulnerabilityList;

            // Add event listeners for vulnerability clicks after elements are added to DOM
            const vulnerabilityElements =
                vulnerabilityListElement.querySelectorAll(
                    "div[id^='vulnerability-']",
                );
            vulnerabilityElements.forEach((element) => {
                element.addEventListener('click', function (e) {
                    e.preventDefault();
                    const vulnerabilityId = this.id.replace(
                        'vulnerability-',
                        '',
                    );
                    console.log(`Clicking on vulnerability ${vulnerabilityId}`);

                    // Find the vulnerability data from the response
                    const vulnerability = data.vulnerabilities.find(
                        (vulnerability) => vulnerability.id == vulnerabilityId,
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
    console.log('Showing details for vulnerability:', vulnerability);

    const detailsElement = document.getElementsByClassName('vulnerability-details')[0];
    const detailsHtml = `
        <div>
            <h2>Vulnerability Details</h2>
            
            <div>
                <div>
                    <h3>Basic Information</h3>
                    <p><strong>Affected Entity:</strong>
                        ${vulnerability.affected_entity || 'N/A'}
                    </p>
                    <p><strong>Identifier:</strong>
                        ${vulnerability.identifier || 'N/A'}
                    </p>
                    <p><strong>Classification:</strong>
                        ${vulnerability.classification || 'N/A'}
                    </p>
                    <p><strong>Location:</strong>
                        ${vulnerability.location || 'N/A'}
                    </p>
                    <p><strong>Affected Component:</strong>
                        ${vulnerability.affected_component || 'N/A'}
                    </p>
                </div>
                
                <div>
                    <h3>Risk Assessment</h3>
                    <p><strong>CVSS Score:</strong>
                        ${vulnerability.cvss_score || 'N/A'}
                    </p>
                    <p><strong>CVSS v3 Code:</strong>
                        ${vulnerability.cvssv3_code || 'N/A'}
                    </p>
                    <p><strong>Likelihood:</strong>
                        ${vulnerability.likelihood || 'N/A'}
                    </p>
                    <p><strong>Remediation Difficulty:</strong>
                        ${vulnerability.remediation_difficulty || 'N/A'}
                    </p>
                    <p><strong>Status:</strong>
                        ${vulnerability.solved ? 'Solved' : 'Open'}
                    </p>
                </div>
            </div>
            
            <div>
                <h3>Description</h3>
                <p>${vulnerability.vulnerabilities_description || 'N/A'}</p>
            </div>
            
            <div>
                <h3>Risk Statement</h3>
                <p>${vulnerability.risk_statement || 'N/A'}</p>
            </div>
              ${
                  vulnerability.reproduction_steps
                      ? `<div>
                    <h3>Reproduction Steps</h3>
                    <pre>${vulnerability.reproduction_steps}</pre>
                </div>`
                      : ''
              }
            
            ${
                vulnerability.impact
                    ? `<div>
                    <h3>Impact</h3>
                    <p>${vulnerability.impact}</p>
                </div>`
                    : ''
            }
            
            <div>
                <h3>Identified Controls</h3>
                <p>${vulnerability.identified_controls || 'N/A'}</p>
            </div>
            
            <div>
                <h3>Residual Risk</h3>
                <p>${vulnerability.residual_risk || 'N/A'}</p>
            </div>
            
            <div>
                <h3>Recommendations</h3>
                <p>${vulnerability.recommendations || 'N/A'}</p>
            </div>
              ${
                  vulnerability.recommended_reading
                      ? `<div>
                    <h3>Recommended Reading</h3>
                    <p>${vulnerability.recommended_reading}</p>
                </div>`
                      : ''
              }
            
            ${
                vulnerability.response
                    ? `<div>
                    <h3>Response</h3>
                    <p>${vulnerability.response}</p>
                </div>`
                    : ''
            }
            
            <div>
                <button onclick="hideVulnerabilityDetails()">Close Details</button>
            </div>
        </div>
    `;

    detailsElement.innerHTML = detailsHtml;
    detailsElement.scrollIntoView({ behavior: 'smooth' });
}

function hideVulnerabilityDetails() {
    const detailsElement = document.getElementsByClassName(
        'vulnerability-details',
    )[0];
    detailsElement.innerHTML = '';
}

// Fetch the test targets when the script loads
fetchTestTargets(test_id);

// Fetch the vulnerabilities of a target when a target is clicked
addEventListener('DOMContentLoaded', function (clickEvent) {
    const targetLinks = document.querySelectorAll("a[id^='target-']");
    targetLinks.forEach((link) => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.id.replace('target-', '');
            fetchVulnerabilities(targetId);
        });
    });
});
