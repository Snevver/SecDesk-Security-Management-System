const titleInputElement = document.getElementById("test-title");
const descriptionInputElement = document.getElementById("test-description");
// const targetContainerElement = document.getElementById("target-container");

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
                titleInputElement.textContent = data.test_name ?? "Loading title...";
            }

            if (descriptionInputElement) {
                descriptionInputElement.textContent = data.test_description ?? "Loading description...";
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
                document.getElementById('target-container');
            if (!data || !data.targets || data.targets.length === 0) {
                targetListElement.innerHTML = '<p>No targets found.</p>';
                return;
            }
            let targetList = '';
            for (let target of data.targets) {
                targetList += `
                    <div id="target-${target.id}">
                        <h3>${target.target_name}</h3>
                        <p>${target.target_description}</p>
                        <a href="/edit-target?target_id=${target.id}">Edit</a>
                        <a href="/delete-target?target_id=${target.id}">Delete</a><br><br>
                    </div>`;
            }

            targetList += `<div id="add-target">
                <br><br><br><a href="/add-target?test_id=${testId}">Add Target</a>
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
