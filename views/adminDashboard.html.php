<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SecDesk SMS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/admin.css">

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
</head>

<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="bi bi-shield-check"></i> Admin Dashboard</h1>
                    <p class="mb-0">SecDesk Security Management System</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end flex-wrap">
                        <div class="user-info me-3">
                            <i class="bi bi-person-circle"></i>
                            <span id="email"><?= $_SESSION['email'] ?? "Unknown" ?></span>
                        </div>
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="bi bi-key"></i> Change Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Action Buttons -->
        <div class="action-buttons">
            <div class="row justify-content-center">
                <div class="col-md-6 mb-3">
                    <button id="create-account-btn" class="btn btn-create btn-lg w-100">
                        <i class="bi bi-person-plus"></i> Create New Account
                    </button>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row">
            <!-- Customers Card -->
            <div class="col-lg-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-people-fill"></i> Customers
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="customers-list" class="user-list overflow-x-hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Employees Card -->
            <div class="col-lg-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge-fill"></i> Employees
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="employees-list" class="user-list overflow-x-hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Admins Card -->
            <div class="col-lg-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge-fill"></i> Admins
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="admins-list" class="user-list overflow-x-hidden"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout Section -->
        <div class="logout-section mt-0">
            <button id="logout-btn" class="btn btn-lg">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="bi bi-key"></i> Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label text-white">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label text-white">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                            <div class="form-text">Password must be at least 8 characters long and contain uppercase, lowercase, and numbers.</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label text-white">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <div id="changePasswordMessage"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="changePasswordForm" class="btn btn-modal-submit">Change Password</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Account Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create New Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="emailForm">
                        <div class="mb-3">
                            <label for="accountType" class="form-label text-white">Account Type</label>
                            <select class="form-select" id="accountType" name="accountType" required>
                                <option value="">Select account type...</option>
                                <option value="customer">Customer</option>
                                <option value="employee">Employee</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="emailInput" class="form-label text-white">Email Address</label>
                            <input type="email" class="form-control" id="emailInput" name="email" required
                                   placeholder="Enter email address">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="emailForm" class="btn btn-modal-submit">Create Account</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script src="/js/changePassword.js"></script>
    <script src="/js/adminDashboard.js"></script>
    <script src="/js/logout.js"></script>
</body>
</html>