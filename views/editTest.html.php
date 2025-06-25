<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit test</title>
        <script src="/js/editTest.js" defer></script>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    
    <body class="p-3 d-flex gap-3">
        <div class="w-50">
            <!-- Title and Description -->
            <header>
                <h1>
                    Editing <span class="fw-bold" id="test-title">Loading title...</span>
                </h1>

                <h3 id="test-description">
                    Loading description...
                </h3>

                <button class="btn btn-dark" id="edit-test-detail-button">
                    Edit Test Detail
                </button>
            </header>

            <!-- Targets -->
            <div class="border rounded p-2 mt-3" id="target-container">
                Loading targets...
            </div>
        </div>

        <div class="w-50 border rounded p-2" id="form-container">
            <!-- Test Detail form -->
            <form class="d-none flex-column gap-3" id="test-detail-form">
                <h1>Test Detail Form</h1>                <div>
                    <label for="test-title-input">Title:</label>
                    <input type="text" id="test-title-input" name="test-title" value="Loading..." required>
                </div>
                <div>
                    <label for="test-description-input">Description:</label>
                    <input type="text" id="test-description-input" name="test-description" value="Loading..." required>
                </div>
                <div>
                    <button type="submit" id="test-submit">Save Changes</button>
                </div>
            </form>

            <!-- Target form -->
            <form class="d-none flex-column gap-3" id="target-form">
                <h1>Target Form</h1>                <div>
                    <label for="target-title-input">Title:</label>
                    <input type="text" id="target-title-input" name="target-title" value="Loading..." required>
                </div>
                <div>
                    <label for="target-description-input">Description:</label>
                    <input type="text" id="target-description-input" name="target-description" value="Loading..." required>
                </div>
                <div>
                    <button type="submit" id="target-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </body>
</html>