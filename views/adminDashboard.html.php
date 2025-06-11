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
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/admin.css">

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />

    <!-- Temporary CSS for Admin Dashboard -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .admin-header {
            background: linear-gradient(135deg, #004185 0%, #0066cc 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .admin-header h1 {
            margin: 0;
            font-weight: 600;
        }        .user-info {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            display: inline-block;
        }

        .btn-outline-light {
            border-color: rgba(255,255,255,0.5);
            color: white;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background-color: white;
            color: #004185;
            border-color: white;
            transform: translateY(-1px);
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #004185;
            font-weight: 600;
            color: #004185;
        }

        .action-buttons {
            margin-bottom: 2rem;
        }

        .btn-create {
            background: linear-gradient(135deg, #004185 0%, #0066cc 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-create:hover {
            background: linear-gradient(135deg, #003366 0%, #0055aa 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,65,133,0.3);
        }

        .user-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .user-item {
            background: #f8f9fa;
            border-left: 4px solid #004185;
            margin-bottom: 0.5rem;
            padding: 1rem;
            border-radius: 0 8px 8px 0;
            transition: all 0.2s ease;
        }

        .user-item:hover {
            background: #e9ecef;
            transform: translateX(4px);
        }

        .user-email {
            font-weight: 600;
            color: #004185;
            font-size: 1.1rem;
        }

        .user-id {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .logout-section {
            margin-top: 3rem;
            text-align: center;
        }

        .btn-logout {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #004185 0%, #0066cc 100%);
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-close {
            filter: invert(1);
        }

        .form-control:focus {
            border-color: #004185;
            box-shadow: 0 0 0 0.2rem rgba(0, 65, 133, 0.25);
        }

        .btn-modal-submit {
            background: linear-gradient(135deg, #004185 0%, #0066cc 100%);
            border: none;
            font-weight: 500;
        }

        .btn-modal-submit:hover {
            background: linear-gradient(135deg, #003366 0%, #0055aa 100%);
        }

        @media (max-width: 768px) {
            .admin-header {
                text-align: center;
            }

            .action-buttons {
                text-align: center;
            }

            .btn-create {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
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
                        <div id="customers-list" class="user-list"></div>
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
                        <div id="employees-list" class="user-list"></div>
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
                        <div id="admins-list" class="user-list"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout Section -->
        <div class="logout-section">
            <button id="logout-btn" class="btn btn-logout btn-lg">
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