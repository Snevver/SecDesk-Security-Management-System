<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit test</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/js/editTest.js" defer></script>
    </head>
    
    <body>
        <h1>Edit test pagina</h1>
        <!-- Form for the test title and description -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <form id="test-form" class="d-flex flex-column p-2 gap-2 col-lg-3">
                <label for="test-title">title</label>
                <input type="text" id="test-title" name="test-title" required>

                <label for="test-description">Description</label>
                <input type="text" id="test-description" name="test-description" required>

                <input type="submit" value="Save" id="test-submit">
            </form>
        </div>


        <!-- List of tests targets -->
    </body>
</html>