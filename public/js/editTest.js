/**
 * Safely set text content to prevent XSS
 * @param {HTMLElement} element - The DOM element to set text content for
 * @param {string} value - The text content to set
 */
function safeSetTextContent(element, value) {
    if (!element) return;

    // Always use textContent (never innerHTML) for user data
    element.textContent = value || "Empty";
}

/**
 * Safely set input value with basic validation
 * @param {HTMLInputElement} element - The input element to set value for
 * @param {string} value - The value to set
 */
function safeSetInputValue(element, value) {
    if (!element) return;

    // Limit input length and sanitize
    let sanitizedValue = (value || "").toString();

    // Remove any null bytes
    sanitizedValue = sanitizedValue.replace(/\0/g, "");

    // Limit length based on input type
    const maxLength = element.maxLength > 0 ? element.maxLength : 1000;
    if (sanitizedValue.length > maxLength) {
        sanitizedValue = sanitizedValue.substring(0, maxLength);
    }

    element.value = sanitizedValue || "Empty";
}

/**
 * Validate input before sending to server
 * @param {string} value - The input value to validate
 * @param {string} fieldName - The name of the field for error messages
 * @param {number} maxLength - The maximum allowed length for the input
 */
function validateInput(value, fieldName, maxLength = 1000) {
    if (typeof value !== "string") {
        value = String(value || "");
    }

    // Remove null bytes
    value = value.replace(/\0/g, "");

    // Check length
    if (value.length > maxLength) {
        console.warn(
            `${fieldName} exceeds maximum length of ${maxLength} characters`
        );
        return value.substring(0, maxLength);
    }

    return value;
}

const urlParams = new URLSearchParams(window.location.search);
const testId = urlParams.get("test_id");

// Get DOM elements
const titleInputElement = document.getElementById("test-title-input");
const descriptionInputElement = document.getElementById(
    "test-description-input"
);

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
    if (entityType === "target") {
        window.currentEditingTargetId = entityId;
    } else if (entityType === "vulnerability") {
        window.currentEditingVulnerabilityId = entityId;
    }

    const apiEndpoints = {
        test: "/api/get-test",
        target: "/api/get-target",
        vulnerability: "/api/get-vulnerability",
    };

    const idFields = {
        test: "test_id",
        target: "target_id",
        vulnerability: "vulnerability_id",
    };

    const endpoint = apiEndpoints[entityType];
    const idField = idFields[entityType];

    if (!endpoint || !idField) {
        console.error(`Invalid entity type: ${entityType}`);
        return;
    }

    fetch(endpoint, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
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
                test: "test-detail-form",
                target: "target-form",
                vulnerability: "vulnerability-form",
            };

            const formId = formIds[entityType];
            if (formId) {
                displayForm(formId);

                // Populate form fields based on entity type
                populateEntityForm(entityType, data);
            } else {
                console.warn(
                    `No form ID mapping found for entity type: ${entityType}`
                );
            }
        })
        .catch((error) => {
            console.error(`Error fetching ${entityType}:`, error);
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
                element: "test-title-input",
                dataField: "test_name",
            },

            description: {
                element: "test-description-input",
                dataField: "test_description",
            },
        },

        target: {
            name: {
                element: "target-title-input",
                dataField: "target_name",
            },

            description: {
                element: "target-description-input",
                dataField: "target_description",
            },
        },

        vulnerability: {
            affected_entity: {
                element: "affected_entity",
                dataField: "affected_entity",
            },
            identifier: {
                element: "identifier",
                dataField: "identifier",
            },
            risk_statement: {
                element: "risk_statement",
                dataField: "risk_statement",
            },
            affected_component: {
                element: "affected_component",
                dataField: "affected_component",
            },
            residual_risk: {
                element: "residual_risk",
                dataField: "residual_risk",
            },
            classification: {
                element: "classification",
                dataField: "classification",
            },
            identified_controls: {
                element: "identified_controls",
                dataField: "identified_controls",
            },
            cvss_score: {
                element: "cvss_score",
                dataField: "cvss_score",
            },
            likelihood: {
                element: "likelihood",
                dataField: "likelihood",
            },
            cvssv3_code: {
                element: "cvssv3_code",
                dataField: "cvssv3_code",
            },
            location: {
                element: "location",
                dataField: "location",
            },
            description: {
                element: "vulnerabilities_description",
                dataField: "vulnerabilities_description",
            },
            reproduction_steps: {
                element: "reproduction_steps",
                dataField: "reproduction_steps",
            },
            impact: {
                element: "impact",
                dataField: "impact",
            },
            remediation_difficulty: {
                element: "remediation_difficulty",
                dataField: "remediation_difficulty",
            },
            recommendations: {
                element: "recommendations",
                dataField: "recommendations",
            },
            recommended_reading: {
                element: "recommended_reading",
                dataField: "recommended_reading",
            },
            response: {
                element: "vulnerability-response-input",
                dataField: "response",
            },
            solved: {
                element: "vulnerability-solved-input",
                dataField: "solved",
            },
        },
    };

    const mapping = fieldMappings[entityType];
    if (!mapping) {
        console.error(`No field mapping found for entity type: ${entityType}`);
        return;
    }

    // Populate each field
    Object.keys(mapping).forEach((fieldType) => {
        const config = mapping[fieldType];
        const element = document.getElementById(config.element);
        const value = data[config.dataField];

        if (element) {
            if (element.type === "checkbox") {
                element.checked = Boolean(value);
            } else if (
                element.tagName === "INPUT" ||
                element.tagName === "TEXTAREA"
            ) {
                // Use safe input value setting
                safeSetInputValue(element, value);
            } else if (element.tagName === "SELECT") {
                element.value = value || "";
            } else {
                // Use safe text content setting
                safeSetTextContent(element, value);
            }
        }
    });
}

/**
 * Fill form elements that already have data (for initial test loading)
 */
function populateFormElement() {
    if (!testId) {
        console.warn("No test ID found in URL parameters");
        return;
    }

    fetch(`/api/get-test`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
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
        })
        .then((data) => {
            // Update the display title and description
            const displayTitle = document.getElementById("test-title");
            const displayDescription =
                document.getElementById("test-description");

            if (displayTitle) {
                safeSetTextContent(displayTitle, data.test_name);
            }

            if (displayDescription) {
                safeSetTextContent(displayDescription, data.test_description);
            }

            // Use the same population logic as editEntity for form inputs
            populateEntityForm("test", data);
        })
        .catch((error) => {
            console.error("Error fetching initial test data:", error);
            // Set fallback values
            if (titleInputElement) {
                titleInputElement.value = "Error loading title";
            }
            if (descriptionInputElement) {
                descriptionInputElement.value = "Error loading description";
            }
        });
}

/**
 * Update the test with the new data
 */
function updateTestData() {
    const titleInput = document.getElementById("test-title-input");
    const descriptionInput = document.getElementById("test-description-input");

    // Validate inputs
    const validatedTitle = validateInput(titleInput.value, "Test Title", 255);
    const validatedDescription = validateInput(
        descriptionInput.value,
        "Test Description",
        2000
    );

    fetch(`/update-test`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            test_id: testId,
            test_name: validatedTitle,
            test_description: validatedDescription,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Refresh all page content
                refreshPageContent();
            } else if (data.error) {
                console.error("Server validation error:", data.error);
            }
        })
        .catch((error) => {
            console.error("Error updating test:", error);
        });
}

/**
 * Update the target with the new data
 */
function updateTargetData() {
    const targetTitleElement = document.getElementById("target-title-input");
    const targetDescriptionElement = document.getElementById(
        "target-description-input"
    );

    if (!targetTitleElement || !targetDescriptionElement) {
        console.error("Error: Target form fields not found");
        return;
    }

    // Get the target ID from the current form or context
    const targetId = window.currentEditingTargetId;

    if (!targetId) {
        console.error("Error: No target ID found for update");
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
                console.error("Error updating target:", data.error);
            } else {
                // Refresh all page content
                refreshPageContent();
            }
        })
        .catch((error) => {
            console.error("Error updating target:", error);
        });
}

/**
 * Update the vulnerability with the new data
 */
function updateVulnerabilityData() {
    const vulnerabilityId = window.currentEditingVulnerabilityId;

    if (!vulnerabilityId) {
        console.error("Error: No vulnerability ID found for update");
        return;
    }

    // Collect all form data
    const formData = {
        vulnerability_id: vulnerabilityId,
        affected_entity: document.getElementById("affected_entity")?.value,
        identifier: document.getElementById("identifier")?.value,
        risk_statement: document.getElementById("risk_statement")?.value,
        affected_component:
            document.getElementById("affected_component")?.value,
        residual_risk: document.getElementById("residual_risk")?.value,
        classification: document.getElementById("classification")?.value,
        identified_controls: document.getElementById("identified_controls")
            ?.value,
        cvss_score: document.getElementById("cvss_score")?.value,
        likelihood: document.getElementById("likelihood")?.value,
        cvssv3_code: document.getElementById("cvssv3_code")?.value,
        location: document.getElementById("location")?.value,
        vulnerabilities_description: document.getElementById(
            "vulnerabilities_description"
        )?.value,
        reproduction_steps:
            document.getElementById("reproduction_steps")?.value,
        impact: document.getElementById("impact")?.value,
        remediation_difficulty: document.getElementById(
            "remediation_difficulty"
        )?.value,
        recommendations: document.getElementById("recommendations")?.value,
        recommended_reading: document.getElementById("recommended_reading")
            ?.value,
        response: document.getElementById("vulnerability-response-input")
            ?.value,
        solved: document.getElementById("vulnerability-solved-input")?.checked,
    };

    fetch(`/update-vulnerability`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (!data.success) {
                console.error("Error updating vulnerability:", data.error);
            } else {
                // Refresh all page content
                refreshPageContent();
            }
        })
        .catch((error) => {
            console.error("Error updating vulnerability:", error);
        });
}

/**
 * Fetch all target data
 */
function fetchTestTargets() {
    console.log("Fetching test targets...");
    fetch(`/api/get-all-targets?test_id=${testId}`)
        .then((response) => {
            if (!response.ok) {
                console.log(response);
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            console.log("Targets fetched:", data);
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
                                ${target.target_name || "Empty"}
                                <span>▼</span>
                            </h3>                            
                            <p>${target.target_description || "Empty"}</p>
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

            targetListElement.innerHTML = targetList;
            console.log("Target list HTML updated");

            // Add event listeners for the edit buttons
            data.targets.forEach((target) => {
                document
                    .querySelector(`.edit-target-button-${target.id}`)
                    .addEventListener("click", (event) => {
                        event.preventDefault();
                        event.stopPropagation(); // Prevent dropdown toggle
                        editEntity("target", target.id);
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
            console.log("Event listeners added to targets");
        })
        .catch((error) => {
            console.error(
                "There was a problem with the fetch operation:",
                error
            );
        });
}

/**
 * Add a new target to the current test
 */
function addNewTarget() {
    console.log("Adding new target for test ID:", testId);

    // Make API call to add the target with empty name and description
    fetch(`/api/add-target?test_id=${testId}`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            console.log("Add target response:", data);
            // Refresh all page content
            refreshPageContent();
        })
        .catch((error) => {
            console.error("Error adding target:", error);
        });
}

/**
 * Add a new vulnerability to a specific target
 * @param {string} targetId The ID of the target to add the vulnerability to
 */
function addNewVulnerability(targetId) {
    console.log("Adding new vulnerability for target ID:", targetId);

    // Make API call to add the vulnerability with empty name and description
    fetch(`/api/add-vulnerability?target_id=${targetId}`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            console.log("Add vulnerability response:", data);
            if (
                data.message === "Vulnerability added successfully" ||
                (data.success && data.vulnerability_id)
            ) {
                console.log(
                    "Vulnerability added successfully, opening dropdown and fetching updated vulnerabilities..."
                );

                // First, ensure the dropdown is open
                const dropdown = document.getElementById(
                    `vulnerabilities-${targetId}`
                );
                const arrow = document.querySelector(
                    `[data-target-id="${targetId}"] span`
                );

                if (dropdown && arrow) {
                    dropdown.style.display = "block";
                    arrow.textContent = "▲";
                    console.log(`Dropdown opened for target ${targetId}`);

                    // Now fetch vulnerabilities for this target to show the new one
                    fetchVulnerabilities(targetId);
                } else {
                    console.error(
                        `Could not find dropdown elements for target ${targetId}`
                    );
                }
            } else {
                console.error(
                    "Error adding vulnerability:",
                    data.error || "Unknown error"
                );
            }
        })
        .catch((error) => {
            console.error("Error adding vulnerability:", error);
        });
}

/**
 * Fetch all vulnerabilty data
 * @param {string} targetId The ID of the target to fetch vulnerabilities for
 */
function fetchVulnerabilities(targetId) {
    console.log(`Fetching vulnerabilities for target ${targetId}...`);

    // Check if the vulnerabilities element exists
    const vulnerabilitiesElement = document.getElementById(
        `vulnerabilities-${targetId}`
    );
    if (!vulnerabilitiesElement) {
        console.error(
            `Vulnerabilities element not found for target ${targetId}`
        );
        return;
    }

    console.log(
        `Making GET request to /api/get-all-vulnerabilities?target_id=${targetId}`
    );
    fetch(`/api/get-all-vulnerabilities?target_id=${targetId}`)
        .then((response) => {
            console.log(
                `Response received for target ${targetId}:`,
                response.status
            );
            if (!response.ok) {
                console.log(response);
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            console.log(
                `Vulnerabilities fetched for target ${targetId}:`,
                data
            );

            let vulnerabilityList = '<div class="border border-dark p-3 mb-3">';

            if (
                !data ||
                !data.vulnerabilities ||
                data.vulnerabilities.length === 0
            ) {
                vulnerabilityList += "<p>No vulnerabilities found.</p>";
            } else {
                for (let vulnerability of data.vulnerabilities) {
                    vulnerabilityList += `
                        <div id="vuln-${vulnerability.id}">
                            <h4>${vulnerability.affected_entity || "Empty"}</h4>
                            <p>${
                                vulnerability.vulnerabilities_description ||
                                "Empty"
                            }</p>
                            ${createActionButtons(
                                "vulnerability",
                                vulnerability.id,
                                `vuln-${vulnerability.id}`
                            )}
                        </div>`;
                }
            }

            // Always add the "Add Vulnerability" button
            vulnerabilityList += `<div id="add-vuln-${targetId}">
                <br><br><br><button class="btn btn-success" onclick="addNewVulnerability(${targetId})">Add Vulnerability</button>
            </div></div>`;

            vulnerabilitiesElement.innerHTML = vulnerabilityList;
            console.log(`Vulnerabilities HTML updated for target ${targetId}`);
        })
        .catch((error) => {
            console.error(
                "There was a problem with the fetch operation:",
                error
            );
        });
}

/**
 * Delete an entity (test, target, or vulnerability)
 * @param {string} entityType - The type of entity ('test', 'target', 'vulnerability')
 * @param {int} entityId - The ID of the entity to delete
 * @param {int} elementId - Optional element ID to remove from DOM after deletion
 * @returns
 */
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

    console.log(`Deleting ${entityName} with ID: ${entityId}`);

    // Refresh page content immediately after confirmation
    refreshPageContent();

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
            console.log(
                `Delete ${entityName} response status:`,
                response.status
            );
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            console.log(`Delete ${entityName} response data:`, data);
            if (data.success) {
                console.log(`${entityName} deleted successfully`);
            } else {
                console.error(`Error deleting ${entityName}:`, data.error);
                // If deletion failed, refresh again to restore correct state
                refreshPageContent();
            }
        })
        .catch((error) => {
            console.error(`Error deleting ${entityName}:`, error);
            // If deletion failed, refresh again to restore correct state
            refreshPageContent();
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

/**
 * Display a specific form by its ID
 * @param {int} formId - The ID of the form to display
 */
function displayForm(formId) {
    // Hide all forms first
    document.querySelectorAll("form").forEach((form) => {
        form.classList.remove("d-flex");
        form.classList.add("d-none");
    });

    // If formId is empty or null, just hide all forms and return
    if (!formId) {
        return;
    }

    const formElement = document.getElementById(formId);

    if (!formElement) {
        console.warn(`Form with ID '${formId}' not found in DOM`);
        return;
    }

    formElement.classList.remove("d-none");
    formElement.classList.add("d-flex");
}

/**
 * Refresh all page content without actually reloading the page
 */
function refreshPageContent() {
    console.log("Starting page content refresh...");

    // Store currently opened vulnerability dropdowns
    const openDropdowns = [];
    document
        .querySelectorAll('[id^="vulnerabilities-"]')
        .forEach((dropdown) => {
            if (dropdown.style.display === "block") {
                const targetId = dropdown.id.replace("vulnerabilities-", "");
                openDropdowns.push(targetId);
            }
        });

    // Refresh the main test data
    console.log("Refreshing test data...");
    populateFormElement();

    // Refresh all targets and their vulnerabilities
    console.log("Refreshing targets...");
    fetchTestTargets();

    // Restore opened dropdowns after a brief delay to allow targets to render
    if (openDropdowns.length > 0) {
        setTimeout(() => {
            openDropdowns.forEach((targetId) => {
                const dropdown = document.getElementById(
                    `vulnerabilities-${targetId}`
                );
                const arrow = document.querySelector(
                    `[data-target-id="${targetId}"] span`
                );

                if (dropdown && arrow) {
                    dropdown.style.display = "block";
                    arrow.textContent = "▲";
                    fetchVulnerabilities(targetId);
                }
            });
        }, 100);
    }

    // Hide any open forms
    displayForm("");

    console.log("Page content refresh completed");
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
        editEntity("test", testId);
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

// Add event listener for add target button
const addTargetBtn = document.getElementById("add-target-btn");
if (addTargetBtn) {
    addTargetBtn.addEventListener("click", (event) => {
        event.preventDefault();
        addNewTarget();
    });
} else {
    console.warn("Add target button not found");
}

// Add event listener for vulnerability form submission
const vulnerabilityForm = document.getElementById("vulnerability-form");
if (vulnerabilityForm) {
    vulnerabilityForm.addEventListener("submit", (event) => {
        event.preventDefault();
        updateVulnerabilityData();
    });
} else {
    console.warn(
        "Vulnerability form not found - vulnerability editing may not work"
    );
}
