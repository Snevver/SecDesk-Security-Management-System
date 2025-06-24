const urlParams = new URLSearchParams(window.location.search);
const testId = urlParams.get("test_id");

// Get DOM elements
const titleInputElement = document.getElementById("test-title-input");
const descriptionInputElement = document.getElementById("test-description-input");

/**
 * Dynamic function to edit any entity (test, target, or vulnerability)
 * @param {string} entityType - The type of entity ('test', 'target', 'vulnerability')
 * @param {number} entityId - The ID of the entity to edit
 */
function editEntity(entityType, entityId) {
    console.debug(`Editing ${entityType} with ID: ${entityId}`);
    
    // Store the current editing entity information for save operations
    window.currentEditingEntityType = entityType;
    window.currentEditingEntityId = entityId;
    if (entityType === 'target') {
        window.currentEditingTargetId = entityId;
    }
    
    const apiEndpoints = {
        test: '/api/get-test',
        target: '/api/get-target',
        vulnerability: '/api/get-vulnerability'
    };
    
    const idFields = {
        test: 'test_id',
        target: 'target_id',
        vulnerability: 'vulnerability_id'
    };
    
    const endpoint = apiEndpoints[entityType];
    const idField = idFields[entityType];
    
    if (!endpoint || !idField) {
        console.error(`Invalid entity type: ${entityType}`);
        return;
    }
    
    fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            [idField]: entityId,
        }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })        
        .then((data) => {
            console.debug(`${entityType} data received:`, data);
            
            // Show the appropriate form with correct ID mapping
            const formIds = {
                test: 'test-detail-form',
                target: 'target-form',
                vulnerability: 'vulnerability-form'
            };
            
            const formId = formIds[entityType];
            if (formId) {
                displayForm(formId);
                
                // Populate form fields based on entity type
                populateEntityForm(entityType, data);
            } else {
                console.warn(`No form ID mapping found for entity type: ${entityType}`);
                alert(`Form not available for ${entityType} editing`);
            }
        })        
        .catch((error) => {
            console.error(`Error fetching ${entityType}:`, error);
            alert(`Error loading ${entityType}: ` + error.message);
        });
}

/**
 * Populate form fields with entity data
 * @param {string} entityType - The type of entity ('test', 'target', 'vulnerability') 
 * @param {object} data - The entity data received from the API
 */
function populateEntityForm(entityType, data) {    
    console.debug(`Populating ${entityType} form with data:`, data);
    const fieldMappings = {
        test: {
            name: { 
                element: 'test-title-input', 
                dataField: 'test_name' 
            },

            description: { 
                element: 'test-description-input', 
                dataField: 'test_description' 
            }
        },

        target: {
            name: { 
                element: 'target-title-input', 
                dataField: 'target_name' 
            },

            description: { 
                element: 'target-description-input', 
                dataField: 'target_description' 
            }
        },

        vulnerability: {
            name: { 
                element: 'vulnerability-title', 
                dataField: 'affected_entity' 
            },

            description: { 
                element: 'vulnerability-description', 
                dataField: 'vulnerabilities_description' 
            }
        }
    };
    
    const mapping = fieldMappings[entityType];
    if (!mapping) {
        console.error(`No field mapping found for entity type: ${entityType}`);
        return;
    }
    
    // Populate each field
    Object.keys(mapping).forEach(fieldType => {
        const config = mapping[fieldType];
        const element = document.getElementById(config.element);
        const value = data[config.dataField];
        
        if (element && value !== undefined) {
            if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                element.value = value;
            } else {
                element.textContent = value;
            }
        }
    });
}

/**
 * Fill form elements that already have data (for initial test loading)
 */
function populateFormElement() {
    if (!testId) {
        console.warn('No test ID found in URL parameters');
        return;
    }
    
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
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })        .then((data) => {
            
            // Update the display title and description
            const displayTitle = document.getElementById('test-title');
            const displayDescription = document.getElementById('test-description');
            
            if (displayTitle && data.test_name) {
                displayTitle.textContent = data.test_name;
            }
            
            if (displayDescription && data.test_description) {
                displayDescription.textContent = data.test_description;
            }
            
            // Use the same population logic as editEntity for form inputs
            populateEntityForm('test', data);
        })
        .catch((error) => {
            console.error('Error fetching initial test data:', error);
            // Set fallback values
            if (titleInputElement) {
                titleInputElement.value = 'Error loading title';
            }
            if (descriptionInputElement) {
                descriptionInputElement.value = 'Error loading description';
            }
        });
}

/**
 * Update the test with the new data
 */
function updateTestData() {
    fetch(`/update-test`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
        },        
        body: JSON.stringify({
            test_id: testId,
            test_name: document.getElementById("test-title-input").value,
            test_description: document.getElementById("test-description-input").value,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                alert(
                    "Error updating test: " + (data.error || "Unknown error")
                );
            } else {
                window.location.reload();
            }

        })
        .catch((error) => {
            console.error("Error updating test:", error);
            alert("Error updating test: " + error.message);
        });
}

/**
 * Update the target with the new data
 */
function updateTargetData() {
    const targetTitleElement = document.getElementById("target-title-input");
    const targetDescriptionElement = document.getElementById("target-description-input");
    
    if (!targetTitleElement || !targetDescriptionElement) {
        alert("Error: Target form fields not found");
        return;
    }
    
    // Get the target ID from the current form or context
    const targetId = window.currentEditingTargetId;
    
    if (!targetId) {
        alert("Error: No target ID found for update");
        return;
    }
    
    fetch(`/update-target`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            target_id: targetId,
            target_name: targetTitleElement.value,
            target_description: targetDescriptionElement.value,
        }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (!data.success) {
                alert(
                    "Error updating target: " + (data.error || "Unknown error")
                );
            } else {
                window.location.reload();
            }
        })
        .catch((error) => {
            console.error("Error updating target:", error);
            alert("Error updating target: " + error.message);
        });
}

/**
 * Fetch all target data
 */
function fetchTestTargets() {
    fetch(`/api/get-all-targets?test_id=${testId}`)
        .then((response) => {
            if (!response.ok) {
                console.log(response);
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            const targetListElement =
                document.getElementById("target-container");
            if (!data || !data.targets || data.targets.length === 0) {
                targetListElement.innerHTML = "<p>No targets found.</p>";
                return;
            }
            let targetList = "";
            for (let target of data.targets) {
                targetList += `
                    <div id="target-${target.id}">
                        <div data-target-id="${target.id}">
                            <h3>
                                ${target.target_name}
                                <span>▼</span>
                            </h3>                            
                            <p>${target.target_description}</p>
                            <button class="btn btn-dark edit-target-button-${
                                target.id
                            }">Edit Target</button>
                            ${createDeleteButton(
                                "target",
                                target.id,
                                `target-${target.id}`
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
            
            // Add event listeners for the edit buttons
            data.targets.forEach((target) => {
                document
                    .querySelector(`.edit-target-button-${target.id}`)
                    .addEventListener("click", (event) => {
                        event.preventDefault();
                        event.stopPropagation(); // Prevent dropdown toggle
                        editEntity('target', target.id);
                    });
            });

            // Add event listeners for dropdown functionality
            const targetHeaders = targetListElement.querySelectorAll(
                "div[data-target-id]"
            );
            targetHeaders.forEach((header) => {
                header.addEventListener("click", function (e) {
                    e.preventDefault();
                    const targetId = this.dataset.targetId;
                    const dropdown = document.getElementById(
                        `vulnerabilities-${targetId}`
                    );
                    const arrow = this.querySelector("span");

                    console.log(`Clicking on target ${targetId}`);

                    // Toggle dropdown visibility
                    if (
                        dropdown.style.display === "none" ||
                        dropdown.style.display === ""
                    ) {
                        // Show dropdown and fetch vulnerabilities
                        dropdown.style.display = "block";
                        arrow.textContent = "▲";
                        fetchVulnerabilities(targetId);
                    } else {
                        // Hide dropdown
                        dropdown.style.display = "none";
                        arrow.textContent = "▼";
                    }
                });
            });
        })
        .catch((error) => {
            console.error(
                "There was a problem with the fetch operation:",
                error
            );
        });
}

/**
 * Fetch all vulnerabilty data
 * @param {string} targetId The ID of the target to fetch vulnerabilities for
 */
function fetchVulnerabilities(targetId) {
    fetch(`/api/get-all-vulnerabilities?target_id=${targetId}`)
        .then((response) => {
            if (!response.ok) {
                console.log(response);
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            const vulnerabilitiesElement = document.getElementById(
                `vulnerabilities-${targetId}`
            );
            if (
                !data ||
                !data.vulnerabilities ||
                data.vulnerabilities.length === 0
            ) {
                vulnerabilitiesElement.innerHTML =
                    "<p>No vulnerabilities found.</p>";
                return;
            }
            let vulnerabilityList = '<div class="border border-dark p-3 mb-3">';
            for (let vulnerability of data.vulnerabilities) {
                vulnerabilityList += `
                    <div id="vuln-${vulnerability.id}">
                        <h4>${vulnerability.affected_entity}</h4>
                        <p>${vulnerability.vulnerabilities_description}</p>
                        ${createActionButtons(
                            "vulnerability",
                            vulnerability.id,
                            `vuln-${vulnerability.id}`
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
                "There was a problem with the fetch operation:",
                error
            );
        });
}

function deleteEntity(entityType, entityId, elementId = null) {
    const entityNames = {
        vulnerability: "vulnerability",
        target: "target",
        test: "test",
    };

    const entityName = entityNames[entityType] || entityType;

    if (!confirm(`Are you sure you want to delete this ${entityName}?`)) {
        return;
    }

    // Construct the API endpoint dynamically
    const endpoint = `/api/delete?${entityType}_id=${entityId}`;

    fetch(endpoint, {
        method: "DELETE",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
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
                    } deleted successfully!`
                );
            }

            alert(
                `${
                    entityName.charAt(0).toUpperCase() + entityName.slice(1)
                } deleted successfully!`
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
    buttonClass = "btn btn-danger"
) {
    return `<button class="${buttonClass}" onclick="event.stopPropagation(); deleteEntity('${entityType}', ${entityId}, '${
        elementId || ""
    }')">Delete</button>`;
}

// Helper function to create action buttons (edit + delete)
function createActionButtons(entityType, entityId, elementId = null) {
    return `
        <button class="btn btn-dark" onclick="event.stopPropagation(); editEntity('${entityType}', ${entityId})">
            Edit ${entityType.charAt(0).toUpperCase() + entityType.slice(1)}
        </button>
        ${createDeleteButton(entityType, entityId, elementId)}
    `;
}

function displayForm(formId) {
    const formElement = document.getElementById(formId);
    
    if (!formElement) {
        console.warn(`Form with ID '${formId}' not found in DOM`);
        return;
    }

    document.querySelectorAll("form").forEach((form) => {
        form.classList.remove("d-flex");
        form.classList.add("d-none");
    });

    formElement.classList.remove("d-none");
    formElement.classList.add("d-flex");
}

fetchTestTargets();
populateFormElement();

// Create event listeners for all buttons
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
        editEntity('test', testId);
    });

// Add event listener for target form submission
const targetForm = document.getElementById("target-form");
if (targetForm) {
    targetForm.addEventListener("submit", (event) => {
        event.preventDefault();
        updateTargetData();
    });
} else {
    console.warn("Target form not found - target editing may not work");
}
