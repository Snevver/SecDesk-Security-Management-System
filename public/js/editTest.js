const titleInputElement = document.getElementById("test-title");
const descriptionInputElement = document.getElementById("test-description");

const urlParams = new URLSearchParams(window.location.search);
const testId = urlParams.get("test_id");

/**
 * Fill any form elements that already have data
 */
function populateFormElement() {
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

function fetchTestTargets() {
    fetch(`/api/targets?test_id=${testId}`)
        .then((response) => {
            if (!response.ok) {
                console.log(response);
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            const targetListElement =
                document.getElementsByClassName('target-list')[0];
            if (!data || !data.targets || data.targets.length === 0) {
                targetListElement.innerHTML = '<p>No targets found.</p>';
                return;
            }
            let targetList = '';
            for (let target of data.targets) {
                targetList += `
                    <div id="target-${target.id}">
                        <form>
                            <label for="target-name-${target.id}">Name</label>
                            <input type="text" name="target_name" value="${target.target_name}" />
                            <label for="target-url-${target.id}">Description</label>
                            <input type="text" name="target_description" value="${target.target_description}" />
                            <button type="button">Save</button>                        </form>
                    </div>`;
            }

            targetList += `<div id="add-target">
                <a href="/add-target?test_id=${testId}">Add Target</a>
            </div>`;

            targetListElement.innerHTML = targetList;
        })
        .catch((error) => {
            console.error(
                'There was a problem with the fetch operation:',
                error,
            );
        });
}

populateFormElement();
fetchTestTargets();

document.getElementById("test-form").addEventListener("submit", (event) => {
    event.preventDefault();
    updateTestData();
});
