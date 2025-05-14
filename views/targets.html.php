<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Targets</title>
    <script src="/js/auth.js"></script>
</head>
<body>

    <!-- Go back to dashboard button -->
    <button id="back-btn" onclick="window.location.href='/'">Back to Dashboard</button>
    <h1>Targets</h1>

    <div id="target-list">Loading targets...</div>

    <!-- Son and Don i dont know how to seperate this script from the html since it uses php to get the testID param from the URL -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const testId = <?php echo json_encode($_GET['test_id'] ?? null); ?>;

            if (!testId) {
                document.getElementById('target-list').innerHTML = '<p>No test ID provided.</p>';
                return;
            }

            fetch(`/api/targets`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ test_id: testId }),
                credentials: 'same-origin',
            })
                .then((response) => response.json())
                .then((data) => {
                    const targetListElement = document.getElementById('target-list');

                    if (!data.success) {
                        targetListElement.innerHTML =
                            '<p>Error loading targets: ' +
                            (data.error || 'Unknown error') +
                            '</p>';
                        return;
                    }

                    if (!data.targets || data.targets.length === 0) {
                        targetListElement.innerHTML = '<p>No targets found.</p>';
                        return;
                    }

                    let html = '<ul>';
                    data.targets.forEach((target) => {
                        html += `<div id="target-${target.id}">
                                    <li>
                                        <strong>Target Name:</strong> ${
                                            target.target_name || 'Not found'
                                        } <br>
                                        <strong>Description:</strong> ${
                                            target.target_description || 'Not found'
                                        }
                                    </li>
                                </div>`;
                    });
                    html += '</ul>';

                    targetListElement.innerHTML = html;
                })
                .catch((error) => {
                    const targetListElement = document.getElementById('target-list');
                    if (targetListElement) {
                        targetListElement.innerHTML =
                            '<p>Error: ' + error.message + '</p>';
                    }
                });
        });
    </script>
</body>
</html>