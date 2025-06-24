const titleInputElement = document.getElementById("test-title");
const descriptionInputElement = document.getElementById("test-description");
// const targetContainerElement = document.getElementById("target-container");

const urlParams = new URLSearchParams(window.location.search);
const testId = urlParams.get("test_id");

/**
 * Fill any form elements that already have data
 */
function populateFormElement() {
    fetch(`/api/get-test`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            test_id: testId,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            console.debug('Test data received:', data);
            if (titleInputElement) {
                if (
                    titleInputElement.tagName === 'INPUT' ||
                    titleInputElement.tagName === 'TEXTAREA'
                ) {
                    titleInputElement.value =
                        data.test_name ?? 'Loading title...';
                } else {
                    titleInputElement.textContent =
                        data.test_name ?? 'Loading title...';
                }
            }

            if (descriptionInputElement) {
                if (
                    descriptionInputElement.tagName === 'INPUT' ||
                    descriptionInputElement.tagName === 'TEXTAREA'
                ) {
                    descriptionInputElement.value =
                        data.test_description ?? 'Loading description...';
                } else {
                    descriptionInputElement.textContent =
                        data.test_description ?? 'Loading description...';
                }
            }
        });
}

/**
 * Update the test with the new data
 */
function updateTestData() {
    fetch(`/update-test`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            test_id: testId,
            test_name:
                titleInputElement.tagName === 'INPUT' ||
                titleInputElement.tagName === 'TEXTAREA'
                    ? titleInputElement.value
                    : titleInputElement.textContent,
            test_description:
                descriptionInputElement.tagName === 'INPUT' ||
                descriptionInputElement.tagName === 'TEXTAREA'
                    ? descriptionInputElement.value
                    : descriptionInputElement.textContent,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                alert(
                    'Error updating test: ' + (data.error || 'Unknown error'),
                );
            }
        })
        .catch((error) => {
            console.error('Error updating test:', error);
            alert('Error updating test: ' + error.message);
        });
}

function fetchTestTargets() {
    fetch(`/api/get-all-targets?test_id=${testId}`)
        .then((response) => {
            if (!response.ok) {
                console.log(response);
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            const targetListElement =
                document.getElementById('target-container');
            if (!data || !data.targets || data.targets.length === 0) {
                targetListElement.innerHTML = '<p>No targets found.</p>';
                return;
            }
            let targetList = '';
            for (let target of data.targets) {
                targetList += `
                    <div id="target-${target.id}">
                        <div data-target-id="${target.id}">
                            <h3>
                                ${target.target_name}
                                <span>▼</span>
                            </h3>                            
                            <p>${target.target_description}</p>
                            <a class="btn btn-dark" href="/edit-target?target_id=${
                                target.id
                            }">Edit Target</a>
                            ${createDeleteButton(
                                'target',
                                target.id,
                                `target-${target.id}`,
                            )}
                        </div>
                        <div id="vulnerabilities-${
                            target.id
                        }" style="display: none;">
                            <div>Loading vulnerabilities...</div>
                        </div>                        
                        <br><br>
                    </div>`;
            }

            targetList += `<div id="add-target">
                <br><br><br><a class="btn btn-success" href="/add-target?test_id=${testId}">Add Target</a>
            </div>`;
            targetListElement.innerHTML = targetList;

            // Add event listeners for dropdown functionality
            const targetHeaders = targetListElement.querySelectorAll(
                'div[data-target-id]',
            );
            targetHeaders.forEach((header) => {
                header.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.dataset.targetId;
                    const dropdown = document.getElementById(
                        `vulnerabilities-${targetId}`,
                    );
                    const arrow = this.querySelector('span');

                    console.log(`Clicking on target ${targetId}`);

                    // Toggle dropdown visibility
                    if (
                        dropdown.style.display === 'none' ||
                        dropdown.style.display === ''
                    ) {
                        // Show dropdown and fetch vulnerabilities
                        dropdown.style.display = 'block';
                        arrow.textContent = '▲';
                        fetchVulnerabilities(targetId);
                    } else {
                        // Hide dropdown
                        dropdown.style.display = 'none';
                        arrow.textContent = '▼';
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

function fetchVulnerabilities(targetId) {
    fetch(`/api/get-all-vulnerabilities?target_id=${targetId}`)
        .then((response) => {
            if (!response.ok) {
                console.log(response);
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            const vulnerabilitiesElement = document.getElementById(
                `vulnerabilities-${targetId}`,
            );
            if (
                !data ||
                !data.vulnerabilities ||
                data.vulnerabilities.length === 0
            ) {
                vulnerabilitiesElement.innerHTML =
                    '<p>No vulnerabilities found.</p>';
                return;
            }
            let vulnerabilityList = '<div class="border border-dark p-3 mb-3">';
            for (let vulnerability of data.vulnerabilities) {
                vulnerabilityList += `
                    <div id="vuln-${vulnerability.id}">
                        <h4>${vulnerability.affected_entity}</h4>
                        <p>${vulnerability.vulnerabilities_description}</p>
                        ${createActionButtons(
                            'vulnerability',
                            vulnerability.id,
                            `vuln-${vulnerability.id}`,
                        )}
                    </div>`;
            }

            vulnerabilityList += `<div id="add-vuln-${targetId}">
                <br><br><br><a class="btn btn-success" href="/add-vulnerability?target_id=${targetId}">Add Vulnerability</a>
            </div></div>`;

            vulnerabilitiesElement.innerHTML = vulnerabilityList;
        })
        .catch((error) => {
            console.error(
                'There was a problem with the fetch operation:',
                error,
            );
        });
}

function deleteVulnerability(vulnerabilityId) {
    deleteEntity('vulnerability', vulnerabilityId, `vuln-${vulnerabilityId}`);
}

function deleteEntity(entityType, entityId, elementId = null) {
    const entityNames = {
        vulnerability: 'vulnerability',
        target: 'target',
        test: 'test',
        customer: 'customer',
        employee: 'employee',
    };

    const entityName = entityNames[entityType] || entityType;

    if (!confirm(`Are you sure you want to delete this ${entityName}?`)) {
        return;
    }

    // Construct the API endpoint dynamically
    const endpoint = `/api/delete?${entityType}_id=${entityId}`;

    fetch(endpoint, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                // Remove the element from the DOM if elementId is provided
                if (elementId) {
                    const element = document.getElementById(elementId);
                    if (element) {
                        element.remove();
                    }
                }
                alert(
                    `${
                        entityName.charAt(0).toUpperCase() + entityName.slice(1)
                    } deleted successfully!`,
                );
            }

            alert(
                `${
                    entityName.charAt(0).toUpperCase() + entityName.slice(1)
                } deleted successfully!`,
            );

            window.location.reload();
        })
        .catch((error) => {
            console.error(`Error deleting ${entityName}:`, error);
            alert(`Error deleting ${entityName}: ` + error.message);
        });
}

// Helper function to create delete buttons dynamically
function createDeleteButton(
    entityType,
    entityId,
    elementId = null,
    buttonClass = 'btn btn-danger',
) {
    return `<button class="${buttonClass}" onclick="deleteEntity('${entityType}', ${entityId}, '${
        elementId || ''
    }')">Delete</button>`;
}

// Helper function to create action buttons (edit + delete)
function createActionButtons(entityType, entityId, elementId = null) {
    const editEndpoint = `/edit-${entityType}?${entityType}_id=${entityId}`;
    return `
        <a class="btn btn-dark" href="${editEndpoint}">Edit ${
        entityType.charAt(0).toUpperCase() + entityType.slice(1)
    }</a>
        ${createDeleteButton(entityType, entityId, elementId)}
    `;
}

function displayForm(formId) {
    const formElement = document.getElementById(formId);

    document.querySelectorAll("form").forEach((form) => {
        form.classList.remove("d-flex");
        form.classList.add("d-none");
    });

    formElement.classList.remove("d-none");
    formElement.classList.add("d-flex");
}

populateFormElement();
fetchTestTargets();

document.getElementById('test-form').addEventListener('submit', (event) => {
    event.preventDefault();
    updateTestData();
});


document
    .getElementById("test-detail-form")
    .addEventListener("submit", (event) => {
        event.preventDefault();
        updateTestData();
    });

document
    .getElementById("edit-test-detail-button")
    .addEventListener("click", (event) => {
        event.preventDefault();
        displayForm("test-detail-form");
    });
