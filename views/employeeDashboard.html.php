<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Employee Dashboard</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/js/employeeDashboard.js" defer></script>
    </head>
      <body class="p-0 m-0 w-100">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-12">                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Employee Dashboard</h1>
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="bi bi-key-fill"></i> Change Password
                            </button>
                            <button id="logout-btn" class="btn btn-outline-danger">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </div>
                    </div>

                    <button id="create-report-btn" class="btn btn-primary mb-4">
                        <i class="bi bi-plus-circle"></i> Create New Report
                    </button>

                    <select id="customer-select" class="form-select d-none mb-4">
                        <option value="" selected disabled>Select a customer</option>
                        <!-- Options will be populated by JavaScript -->
                    </select>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-person-circle"></i>
                                Logged in as <span id="email" class="text-primary"><?=$_SESSION['email'] ?? "Unknown"?></span>
                            </h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="bi bi-clock-history"></i>
                                        Tests in Progress
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="reports-in-progress">
                                        <!-- Content will be loaded by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-check-circle"></i>
                                        Completed Tests
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="completed-reports">
                                        <!-- Content will be loaded by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>

            <!-- Change Password Modal -->
            <div class="modal fade" id="changePasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changePasswordModalLabel">
                                <i class="bi bi-key-fill"></i>
                                Change Password
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form id="changePasswordForm">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="currentPassword" class="form-label text-white">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" required>
                                </div>
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label text-white">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" required>
                                    <div class="form-text">Password should be at least 8 characters long.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label text-white">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" required>
                                </div>
                                <div id="passwordError" class="text-danger d-none"></div>
                                <div id="passwordSuccess" class="text-success d-none"></div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="changePasswordSubmit">
                                    <i class="bi bi-check-lg"></i> Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
        <script src="/js/logout.js"></script>
        <script src="/js/changePassword.js"></script>
    </body>
</html>