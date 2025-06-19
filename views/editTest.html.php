<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit test</title>
        <script src="/js/editTest.js" defer></script>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                background: white;
                color: black;
            }
            .header {
                border: 1px solid black;
                padding: 20px;
                margin-bottom: 20px;
            }
            .content {
                display: flex;
                gap: 20px;
            }
            .left, .right {
                flex: 1;
                border: 1px solid black;
                padding: 20px;
            }
            input, button {
                margin: 5px 0;
                padding: 5px;
                border: 1px solid black;
                background: white;
                color: black;
            }
            h1, h3 {
                margin-top: 0;
            }
        </style>
    </head>
    
    <body>
        <a href="/employee">Back to Dashboard</a>

        <div class="header">
            <h1>Edit Test</h1>
            <form id="test-form">
                <div>
                    <label for="test-title">Title:</label>
                    <input type="text" id="test-title" name="test-title" value="Loading..." required>
                </div>
                <div>
                    <label for="test-description">Description:</label>
                    <input type="text" id="test-description" name="test-description" value="Loading..." required>
                </div>
                <div>
                    <button type="submit" id="test-submit">Save Changes</button>
                </div>
            </form>
        </div>

        <div class="content">
            <div class="left">
                <h3>Targets</h3>
                <div class="target-list">
                    <!-- Targets will be loaded here -->
                </div>
            </div>

            <div class="right">
                <h3>Vulnerabilities</h3>
                <div class="vulnerability-content">
                    <!-- Vulnerability form/content will be loaded here -->
                </div>
            </div>
        </div>
    </body>
</html>