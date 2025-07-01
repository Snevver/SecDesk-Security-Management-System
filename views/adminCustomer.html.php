<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Admin - Customer Management | SecDesk</title>
    <link href="/css/bootstrap.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .customer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }        .severity-high { color: #dc3545; font-weight: bold; }
        .severity-medium { color: #fd7e14; font-weight: bold; }
        .severity-low { color: #ffc107; font-weight: bold; }
        .severity-critical { color: #8b0000; font-weight: bold; }
        .status-open { color: #dc3545; }
        .status-solved { color: #28a745; }
        .status-fixed { color: #28a745; }
        .status-in-progress { color: #17a2b8; }        
        .nav-tabs .nav-link.active {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        .clickable-link:hover {
            color: #0056b3 !important;
            text-decoration: underline !important;
        }        .filter-badge {
            background-color: #667eea;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
        }
        
        /* Vulnerability Card Styles */
        .vulnerability-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        
        .vulnerability-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }
        
        .vulnerability-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            position: relative;
        }
        
        .vulnerability-header:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        }
        
        .vulnerability-body {
            padding: 0;
            background: #fff;
        }
        
        .vulnerability-details {
            padding: 1.5rem;
            display: none;
        }
        
        .vulnerability-details.show {
            display: block;
        }
        
        .detail-section {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .detail-section h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #667eea;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 0.5rem;
            align-items: flex-start;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            min-width: 120px;
            margin-right: 1rem;
        }
        
        .detail-value {
            flex: 1;
            color: #495057;
        }
        
        .expand-indicator {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.2s ease;
        }
        
        .expand-indicator.expanded {
            transform: translateY(-50%) rotate(180deg);
        }
        
        .cvss-score {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .cvss-critical { background: #8b0000; color: white; }
        .cvss-high { background: #dc3545; color: white; }
        .cvss-medium { background: #fd7e14; color: white; }
        .cvss-low { background: #ffc107; color: #212529; }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .status-solved { background: #28a745; color: white; }
        .status-open { background: #dc3545; color: white; }
        
        .vuln-actions {
            padding: 1rem;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="customer-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-0">Customer Management</h1>
                        <p class="mb-0 opacity-75">Manage customer data, tests, targets, and vulnerabilities</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-light" onclick="window.history.back()">
                            <i class="bi bi-arrow-left"></i> Back to Admin Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Customer Info Card -->
            <div class="row mb-4">
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
                                        <h4 class="text-primary mb-0" id="total-tests">0</h4>
                                        <small class="text-muted">Tests</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="text-info mb-0" id="total-targets">0</h4>
                                        <small class="text-muted">Targets</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="text-danger mb-0" id="total-vulnerabilities">0</h4>
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

    <script src="/js/bootstrap.js"></script>   
    <script src="/js/adminCustomer.js"></script> 
</body>
</html>