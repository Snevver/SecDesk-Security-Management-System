<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; font-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:; connect-src 'self';">
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
                    <input type="text" id="test-title-input" name="test-title" value="" required>
                </div>
                <div>
                    <label for="test-description-input">Description:</label>
                    <input type="text" id="test-description-input" name="test-description" value="" required>
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
                    <input type="text" id="target-title-input" name="target-title" value="" required>
                </div>
                <div>
                    <label for="target-description-input">Description:</label>
                    <input type="text" id="target-description-input" name="target-description" value="" required>
                </div>
                <div>
                    <button type="submit" id="target-submit">Save Changes</button>
                </div>
            </form>

            <!-- Vulnerability Form -->
            <form class="d-none flex-column gap-3" id="vulnerability-form">
                <h1>Vulnerability Form</h1>           

                <div>
                    <label for="affected_entity">Vulnerability Name:</label>
                    <input type="text" id="affected_entity" name="affected_entity" value="" required>
                </div>

                <div>
                    <label for="identifier">Identifier:</label>
                    <input type="text" id="identifier" name="identifier" value="" required>
                </div>

                <div>
                    <label for="risk_statement">Risk statement:</label>
                    <input type="text" id="risk_statement" name="risk_statement" value="" required>
                </div>

                <div>
                    <label for="affected_component">Affected Component:</label>
                    <input type="text" id="affected_component" name="affected_component" value="" required>
                </div>

                <div>
                    <label for="residual_risk">Residual Risk:</label>
                    <select id="residual_risk" name="residual_risk" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>

                <div>
                    <label for="classification">classification:</label>
                    <input type="text" id="classification" name="classification" value="" required>
                </div>

                <div>
                    <label for="identified_controls">identified Controls:</label>
                    <input type="text" id="identified_controls" name="identified_controls" value="" required>
                </div>

                <div>
                    <label for="cvss_score">CVSS Score:</label>
                    <input type="number" id="cvss_score" name="cvss_score" step="0.1" min="0" max="10" value="0" required>
                </div>

                <div>
                    <label for="likelihood">Likelihood:</label>
                    <select name="likelihood" id="likelihood">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>

                <div>
                    <label for="cvssv3_code">CVSSV3 Code:</label>
                    <input type="text" id="cvssv3_code" name="cvssv3_code" value="0" required>
                </div>

                <div>
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" value="" required>
                </div>

                <div>
                    <label for="vulnerabilities_description">Description:</label>
                    <textarea id="vulnerabilities_description" name="vulnerabilities_description" rows="3" required></textarea>
                </div>

                <div>
                    <label for="reproduction_steps">Reproduction Steps:</label>
                    <textarea id="reproduction_steps" name="reproduction_steps" rows="5" required></textarea>
                </div>

                <div>
                    <label for="impact">Impact:</label>
                    <textarea id="impact" name="impact" rows="3" required></textarea>
                </div>

                <div>
                    <label for="remediation_difficulty">Remediation Difficulty:</label>
                    <select name="remediation_difficulty" id="remediation_difficulty">
                        <option value="Easy">Easy</option>
                        <option value="Medium">Medium</option>
                        <option value="Hard">Hard</option>
                    </select>
                </div>

                <div>
                    <label for="recommendations">Recommendations:</label>
                    <textarea id="recommendations" name="recommendations" rows="3" required></textarea>
                </div>

                <div>
                    <label for="recommended_reading">Recommended Reading:</label>
                    <textarea id="recommended_reading" name="recommended_reading" rows="2" required></textarea>
                </div>

                <div>
                    <label for="vulnerability-response-input">Response:</label>
                    <textarea id="vulnerability-response-input" name="vulnerability-response" rows="2"></textarea>
                </div>

                <div>
                    <button type="submit" id="vulnerability-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </body>
</html>