<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit test</title>
        <!-- CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/js/editTest.js" defer></script>
    </head>
    
    <body class="p-2">
        <h1 class="text-center display-4 fw-bold">Edit test</h1>

        <div class="d-flex flex-row">
            <!-- TEST INFO -->
            <div class="col-4 d-flex flex-column align-items-center">
                <h1>Test Detail</h1>

                <div class="d-flex align-items-center">
                    <form id="test-form" class="d-flex flex-column gap-3">
                        <div class="d-flex flex-column">
                            <label for="test-title">title</label>
                            <input class="rounded" type="text" id="test-title" name="test-title" value="Loading..." required>
                        </div>

                        <div class="d-flex flex-column">
                            <label for="test-description">Description</label>
                            <input class="rounded" type="text" id="test-description" name="test-description" value="Loading..." required>
                        </div>

                        <input class="btn btn-dark" type="submit" value="Save" id="test-submit">
                    </form>
                </div>
            </div>

            <!-- TARGETS -->
            <div class="col-4 d-flex flex-column align-items-center">
                <h1>Targets</h1>
                
                <div class="d-flex align-items-center">
                </div>
            </div>

            <!-- VULNERABILITY FORM -->
            <div class="col-4 d-flex flex-column align-items-center">
                <h1>Vulnerability</h1>
                
                <div class="d-flex align-items-center target-list">
                </div>
            </div>
        </div>
    </body>
</html>