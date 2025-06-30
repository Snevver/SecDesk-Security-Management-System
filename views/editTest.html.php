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
            
            <!-- Add Target Button -->
            <div class="mt-3">
                <button class="btn btn-success" id="add-target-btn">
                    <i class="bi bi-plus-circle"></i> Add Target
                </button>
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
                <h1>Target Form</h1> 

                <div>
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

            <!-- Vulnerability Form -->
            <form class="d-none flex-column gap-3" id="vulnerability-form">
                <h1>Vulnerability Form</h1>           

                <div>
                    <label for="vulnerability-title-input">Title:</label>
                    <input type="text" id="vulnerability-title-input" name="vulnerability-title" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-identifier-input">Identifier:</label>
                    <input type="text" id="vulnerability-identifier-input" name="vulnerability-identifier" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-risk-statement-input">Risk statement:</label>
                    <input type="text" id="vulnerability-risk-statement-input" name="vulnerability-risk-statement" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-affected-component-input">Affected Component:</label>
                    <input type="text" id="vulnerability-affected-component-input" name="vulnerability-affected-component" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-residual-risk-input">Residual Risk:</label>
                    <input type="text" id="vulnerability-residual-risk-input" name="vulnerability-residual-risk" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-classification-input">classification:</label>
                    <input type="text" id="vulnerability-classification-input" name="vulnerability-classification" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-identified-controls-input">identified Controls:</label>
                    <input type="text" id="vulnerability-identified-controls-input" name="vulnerability-identified-controls" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-cvss-score-input">CVSS Score:</label>
                    <input type="number" id="vulnerability-cvss-score-input" name="vulnerability-cvss-score" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-likelihood-input">Likelihood:</label>
                    <input type="text" id="vulnerability-likelihood-input" name="vulnerability-likelihood" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-cvssv3-code-input">CVSSV3 Code:</label>
                    <input type="text" id="vulnerability-cvssv3-code-input" name="vulnerability-cvssv3-code" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-location-input">Location:</label>
                    <input type="text" id="vulnerability-location-input" name="vulnerability-location" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-description-input">Description:</label>
                    <input type="text" id="vulnerability-description-input" name="vulnerability-description" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-reproduction-steps-input">Reproduction Steps:</label>
                    <input type="text" id="vulnerability-reproduction-steps-input" name="vulnerability-reproduction-steps" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-impact-input">Impact:</label>
                    <input type="text" id="vulnerability-impact-input" name="vulnerability-impact" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-remediation-difficulty-input">Remediation Difficulty:</label>
                    <input type="text" id="vulnerability-remediation-difficulty-input" name="vulnerability-remediation-difficulty" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-response-input">Response:</label>
                    <input type="text" id="vulnerability-response-input" name="vulnerability-response" value="Loading..." required>
                </div>

                <div>
                    <label for="vulnerability-solved-input">Solved?</label>
                    <input type="radio" id="vulnerability-solved-input" name="vulnerability-solved" value="Loading..." required>
                </div>

                <div>
                    <button type="submit" id="vulnerability-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </body>
</html>