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
            Click an edit button to load a form...
        </div>
    </body>
</html>