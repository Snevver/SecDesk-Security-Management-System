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
        <!-- Custom CSS -->
        <link rel="stylesheet" href="/css/edit.css">
    </head>

<body class="p-0 m-0 vh-100 d-flex flex-column overflow-hidden">
    <!-- HEADER -->
    <header id="mainHeader" class="d-flex align-items-center justify-content-start justify-content-sm-start">
        <div class="d-flex flex-nowrap justify-content-between align-items-center text-center h-100 w-100">
            <!-- Logo -->
            <div class="d-none d-md-block">
                <img src="images/secdesk-logo.webp" alt="SecDesk Logo" class="logo p-0 w-auto">
            </div>

            <!-- Title -->
            <div class="h-100 d-flex align-items-center">
                <h1 class="fs-2 m-0 d-none d-lg-flex text-center h-100 d-flex align-items-center">
                <span>Security Management System</span>
            </h1>

            <img src="images/sms-simple.webp" alt="SecDesk Logo" class="img-fluid d-lg-none" style="max-height: 60px;">
        </div>

        <div class="d-none d-md-block" style="min-width: 101.4px;"></div>
        </div>
    </header>

    <!-- Target List Header -->
    <header class="d-flex">
        <div id="targetSubheader" class="collapse show mb-1 mt-1 ms-1 me-1 targetListHeader rounded subHeader1 d-flex justify-content-between align-items-center">
            <div class="h-100 w-100 fs-2 d-flex flex-nowrap justify-content-between align-items-center">
                <a href="/" class="back btn mb-0 ms-1 p-0"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                data-bs-title="Back to tests"
                data-bs-custom-class="custom-tooltip">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                <h3 class="headerTitle d-none d-xl-block text-center m-0">Targets</h3>

                <div style="width: 36px"></div>

            </div>
        </div>

        <!-- Edit Section Header -->
        <div class="mb-1 mt-1 ms-0 me-1 targetListHeader rounded flex-grow-1 d-flex justify-content-center align-items-center">
            <div id="test-title" class="h-100 fs-2 d-flex flex-nowrap justify-content-start align-items-center text-center">
                <div class="d-flex justify-content-center align-items-center py-5 w-100">
                    Loading title
                <div class="spinner-border text-primary ms-2" role="status" aria-label="Loading"></div>
                </div>
            </div>

        </div>
    </header>

     <!-- CONTENT -->
    <div id="targetsMain" class="d-flex flex-grow-1 overflow-hidden">

        <!-- Left side container for sidebar -->
        <div class="d-flex me-1 w-100">
            <!-- Targetlist -->
            <div id="targetSidebarDesktop" class="targetSidebar ms-1 d-flex flex-column">
                <div id="targetAccordionDesktop" class="accordion accordion-flush p-0 m-0 target-list flex-grow-1 overflow-auto"></div>
                <div class="form-submit-footer border-top p-2 d-flex justify-content-end">

                <!-- Add Target Button -->
                <button class="btn me-3" id="add-target-btn">
                    <i class="bi bi-plus-circle"></i> Add Target
                </button>
                <!-- Edit Test Detail Button -->
                <button class="btn" id="edit-test-detail-button">
                    Edit Test Detail
                </button>
            </div>
        </div>

            <!-- Edit section -->
            <div id="editSection" class="h-100 d-flex w-100 ms-1 me-0 flex-grow-1 overflow-auto rounded">
                <div class="d-flex flex-column h-100 w-100 flex-grow-1">

                    <div class="pt-2 ps-2 pe-2" id="form-container">
                        <!-- Test Detail form -->
                        <form class="d-none flex-column gap-3" id="test-detail-form">
                            <div class="divTable modern-table rounded shadow-sm mb-4">
                                <div class="divTableBody">
                                    <div class="divTableRow">
                                        <div class="divTableCell py-2 px-2"><strong>Title:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input class="w-100" type="text" id="test-title-input" name="test-title" value="Loading..." required>
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell py-2 px-2"><strong>Description:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input class="w-100" type="text" id="test-description-input" name="test-description" value="Loading..." required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-submit-footer position-sticky bottom-0 bg-white border-top p-2 d-flex justify-content-end">
                                <button type="submit" id="test-submit" class="btn btn-primary px-4">Save Changes</button>
                            </div>
                        </form>

                        <!-- Target form -->
                        <form class="d-none flex-column gap-3" id="target-form">
                            <div class="divTable modern-table rounded shadow-sm mb-4">
                                <div class="divTableBody">
                                    <div class="divTableRow">
                                        <div class="divTableCell py-2 px-2"><strong>Title:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="target-title-input" name="target-title" value="Loading..." required>
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell py-2 px-2"><strong>Description:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="target-description-input" name="target-description" value="Loading..." required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-submit-footer position-sticky bottom-0 bg-white border-top p-2 d-flex justify-content-end">
                                <button type="submit" id="target-submit" class="btn btn-primary px-4">Save Changes</button>
                            </div>
                        </form>

                        <!-- Vulnerability Form -->
                        <form class="d-none flex-column" id="vulnerability-form">
                            <div class="divTable modern-table rounded shadow-sm mb-4">
                                <div class="divTableBody">
                                    <div class="divTableRow" style="background: #f8f9fa;">
                                        <div class="divTableCell py-2 px-2"><strong>Vulnerability Name:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="affected_entity" name="affected_entity" required>
                                        </div>
                                        <div class="divTableCell py-2 px-2"><strong>Identifier:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="identifier" name="identifier" required>
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell py-2 px-2"><strong>CVSS Score:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="number" id="cvss_score" name="cvss_score" step="0.1" min="0" max="10" value="0" required>
                                        </div>
                                        <div class="divTableCell py-2 px-2"><strong>CVSS v3 Code:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="cvssv3_code" name="cvssv3_code" value="0" required>
                                        </div>
                                    </div>
                                    <div class="divTableRow" style="background: #f8f9fa;">
                                        <div class="divTableCell py-2 px-2"><strong>Classification:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="classification" name="classification" required>
                                        </div>
                                        <div class="divTableCell py-2 px-2"><strong>Affected Component:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="affected_component" name="affected_component" required>
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell py-2 px-2"><strong>Location:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="location" name="location" required>
                                        </div>
                                        <div class="divTableCell py-2 px-2"><strong>Identified Controls:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="identified_controls" name="identified_controls" required>
                                        </div>
                                    </div>
                                    <div class="divTableRow" style="background: #f8f9fa;">
                                        <div class="divTableCell py-2 px-2"><strong>Likelihood:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <select name="likelihood" id="likelihood">
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                                <option value="Critical">Critical</option>
                                            </select>
                                        </div>
                                        <div class="divTableCell py-2 px-2"><strong>Residual Risk:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <select id="residual_risk" name="residual_risk" required>
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                                <option value="Critical">Critical</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="divTableRow">
                                        <div class="divTableCell py-2 px-2"><strong>Risk Statement:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <input type="text" id="risk_statement" name="risk_statement" required>
                                        </div>
                                        <div class="divTableCell py-2 px-2"><strong>Remediation Difficulty:</strong></div>
                                        <div class="divTableCell py-2 px-2">
                                            <select name="remediation_difficulty" id="remediation_difficulty">
                                                <option value="Easy">Easy</option>
                                                <option value="Medium">Medium</option>
                                                <option value="Hard">Hard</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Textareas below the table -->
                            <div class="mt-3">
                                <label for="vulnerabilities_description"><strong>Description:</strong></label>
                                <textarea class="form-control" id="vulnerabilities_description" name="vulnerabilities_description" rows="3" required></textarea>
                            </div>

                            <div class="mt-3">
                                <label for="reproduction_steps"><strong>Reproduction Steps:</strong></label>
                                <textarea class="form-control" id="reproduction_steps" name="reproduction_steps" rows="5" required></textarea>
                            </div>

                            <div class="mt-3">
                                <label for="impact"><strong>Impact:</strong></label>
                                <textarea class="form-control" id="impact" name="impact" rows="3" required></textarea>
                            </div>

                            <div class="mt-3">
                                <label for="recommendations"><strong>Recommendations:</strong></label>
                                <textarea class="form-control" id="recommendations" name="recommendations" rows="3" required></textarea>
                            </div>

                            <div class="mt-3">
                                <label for="recommended_reading"><strong>Recommended Reading:</strong></label>
                                <textarea class="form-control" id="recommended_reading" name="recommended_reading" rows="2" required></textarea>
                            </div>

                            <div class="mt-3">
                                <label for="vulnerability-response-input"><strong>Response:</strong></label>
                                <textarea class="form-control" id="vulnerability-response-input" name="vulnerability-response" rows="2"></textarea>
                            </div>

                            <div class="mt-3 mb-3">
                                <label for="vulnerability-solved-input"><strong>Solved?</strong></label>
                                <input type="checkbox" id="vulnerability-solved-input" name="vulnerability-solved">
                            </div>

                            <div class="form-submit-footer position-sticky bottom-0 bg-white border-top p-2 d-flex justify-content-end">
                                <button type="submit" id="vulnerability-submit" class="btn btn-primary px-4">Save Changes</button>
                            </div>
                        </form>

                </div>

            </div>
        </div>
    </div>

    </body>
</html>