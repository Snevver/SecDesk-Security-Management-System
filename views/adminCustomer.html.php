<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Admin - Customer Management | SecDesk</title>
    <link href="/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header id="mainHeader" class="d-flex align-items-center justify-content-start justify-content-sm-start">
        <div class="d-flex flex-nowrap justify-content-between align-items-center text-center h-100 w-100">
            <!-- Logo -->
            <div class="d-none d-md-block">
                <img src="/images/secdesk-logo.webp" alt="SecDesk Logo" class="logo p-0 w-auto">
            </div>

            <!-- Title -->
            <div class="h-100 d-flex align-items-center">
                <h1 class="m-0 d-none d-lg-flex text-center h-100 d-flex align-items-center">
                    <span>Security Management System</span>
                </h1>

                <img src="/images/sms-simple.webp" alt="SecDesk Logo" class="img-fluid d-lg-none">
            </div>

            <div class="d-none d-md-block" style="min-width: 101.4px;"></div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <div class="content-wrapper d-flex">
        <div id="sidebar" class="d-flex flex-column flex-shrink-0 bg-light">
            <!-- Logo only -->
            <a href="/" class="d-block p-3 link-dark text-decoration-none" title="Icon-only" data-bs-toggle="tooltip" data-bs-placement="right">
                <img src="/images/logo-only.webp" alt="SecDesk Logo" class="logo-only">
            </a>

            <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
                <!-- Dashboard Button -->
                <li class="nav-item">
                    <a href="#" class="nav-link active py-3 border-bottom rounded-0" aria-current="page" title="Home" data-bs-toggle="tooltip" data-bs-placement="right">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door" viewBox="0 0 16 16">
                            <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4z" />
                        </svg>
                    </a>
                </li>
            </ul>

            <!-- Logout Button -->
            <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal" class="d-flex align-items-center justify-content-center p-3 link-dark text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                </svg>
            </a>
        </div>

        <!-- DASHBOARD -->
        <main class="d-flex justify-content-center w-100 align-items-end">
            <section id="userDashboard" class="w-100">
                <div class="row d-flex text-align-center justify-content-center align-items-center">
                    <section id="testList" class="container-fluid p-0 ms-3 me-3 rounded">

						<header id="testListHeader" class="mb-3 mt-3 ms-xxl-0 me-xxl-0 ms-3 me-3 rounded">
							<div class="h-100 fs-2 d-flex flex-nowrap justify-content-between align-items-center">
								<h2 class="mb-0 flex-grow-1 text-center" >Customer Detail</h2>
							</div>
						</header>

						<div id="testListAccordion" class="accordion row ms-3 me-3">
                            <div class="container-fluid">
        <div class="container">
            <!-- Customer Info Card -->
            <div class="row mb-4" style="height: 80%; overflow-y: auto;">
                <div class="col-12">
                    <div class="stats-card">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 id="customer-name">Loading Customer...</h3>
                                <p class="text-muted" id="customer-email">Loading...</p>
                                <p class="text-muted">
                                    <small>Customer ID: <span id="customer-id">-</span> | 
                                    Joined: <span id="customer-joined">-</span></small>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h4 class="highlightPurple mb-0" id="total-tests">0</h4>
                                        <small class="text-muted">Tests</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="highlightPurple mb-0" id="total-targets">0</h4>
                                        <small class="text-muted">Targets</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="highlightPurple mb-0" id="total-vulnerabilities">0</h4>
                                        <small class="text-muted">Vulnerabilities</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs mb-4" id="customerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tests-tab" data-bs-toggle="tab" data-bs-target="#tests" type="button" role="tab">
                        Tests
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="targets-tab" data-bs-toggle="tab" data-bs-target="#targets" type="button" role="tab">
                        Targets
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vulnerabilities-tab" data-bs-toggle="tab" data-bs-target="#vulnerabilities" type="button" role="tab">
                        Vulnerabilities
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="customerTabContent">
                <!-- Tests Tab -->
                <div class="tab-pane fade show active" id="tests" role="tabpanel">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Customer Tests</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">                                
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Pentester</th>
                                        <th>Test Date</th>
                                    </tr>
                                </thead>
                                <tbody id="tests-table-body">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading tests...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Targets Tab -->
                <div class="tab-pane fade" id="targets" role="tabpanel">
                    <div class="stats-card">
                        <h4 class="mb-3">Customer Targets</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">                                
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Test</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>                                
                                <tbody id="targets-table-body">
                                    <tr>
                                        <td colspan="5" class="text-center">Loading targets...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>                
                
                <!-- Vulnerabilities Tab -->
                <div class="tab-pane fade" id="vulnerabilities" role="tabpanel">
                    <div class="stats-card">
                        <h4 class="mb-3">Customer Vulnerabilities</h4>
                        <div id="vulnerabilities-container">
                            <div class="text-center p-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading vulnerabilities...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading vulnerabilities...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                        </div>
					</div>
				</section>
			</main>		
		</div>
    <script src="/js/bootstrap.js"></script>   
    <script src="/js/adminCustomer.js"></script> 
</body>
</html>