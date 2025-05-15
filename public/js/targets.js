document.addEventListener("DOMContentLoaded", function () {
    // Extract ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const testId = urlParams.get("id");
    console.debug(testId);

    if (!testId) {
        document.getElementById("target-list").innerHTML =
            "<p>No test ID provided.</p>";
        return;
    }
    fetch(`/api/targets`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ test_id: testId }),
        credentials: "same-origin",
    })
        .then((response) => response.json())
        .then((data) => {
            const targetListElement = document.getElementById("target-list");

            if (!data.success) {
                targetListElement.innerHTML =
                    "<p>Error loading targets: " +
                    (data.error || "Unknown error") +
                    "</p>";
                return;
            }

            if (!data.targets || data.targets.length === 0) {
                targetListElement.innerHTML = "<p>No targets found.</p>";
                return;
            }

            let html = "<ul>";
            data.targets.forEach((target) => {
                html += `<div id="target-${target.id}">
                                    <li>
                                        <strong>Target Name:</strong> ${
                                            target.target_name || "Not found"
                                        } <br>
                                        <strong>Description:</strong> ${
                                            target.target_description ||
                                            "Not found"
                                        }
                                    </li>
                                </div>`;
            });
            html += "</ul>";

            targetListElement.innerHTML = html;
        })
        .catch((error) => {
            const targetListElement = document.getElementById("target-list");
            if (targetListElement) {
                targetListElement.innerHTML =
                    "<p>Error: " + error.message + "</p>";
            }
        });
});
