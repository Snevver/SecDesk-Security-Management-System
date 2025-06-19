const titleInputElement = document.getElementById("test-title");
const descriptionInputElement = document.getElementById("test-description");

/**
 * Fill any form elements that already have data
 */
function populateFormElement() {
    const urlParams = new URLSearchParams(window.location.search);
    const testId = urlParams.get("test_id");

    fetch(`/api/get-test-data`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            test_id: testId,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            console.debug("Test data received:", data);
            if (titleInputElement) {
                titleInputElement.value = data.test_name ?? null;
            }

            if (descriptionInputElement) {
                descriptionInputElement.value = data.test_description ?? null;
            }
        });
}

/**
 * Update the test with the new data
 */
function updateTestData() {
    const urlParams = new URLSearchParams(window.location.search);
    const testId = urlParams.get("test_id");

    fetch(`/update-test`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            test_id: testId,
            test_name: titleInputElement.value,
            test_description: descriptionInputElement.value,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                alert(
                    "Error updating test: " + (data.error || "Unknown error")
                );
            }
        })
        .catch((error) => {
            console.error("Error updating test:", error);
            alert("Error updating test: " + error.message);
        });
}

populateFormElement();

document.getElementById("test-form").addEventListener("submit", (event) => {
    event.preventDefault();
    updateTestData();
});
