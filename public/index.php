<?php

//======================================================================
// ROUTE ALL REQUESTS
//======================================================================

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

// Declare all the used namespaces
use Ssms\Exceptions\HTTPException;
use Ssms\Controllers\ErrorController;
use Ssms\Logger;

$app = new Ssms\Application();
$app->init();
$uri = $app->getUri();

// Route requests
try {
    $app->applyAuthMiddleware();

    switch ($uri) {
        // Routes to views
        case '/':
            $app->handleDashboardRoute();
            break;

        case '/login':
            if (session_status() === PHP_SESSION_NONE) session_start();
            include DIR_VIEWS . 'login.html.php';
            break;

        case '/targets':
            $app->handleAuthenticatedRoute('targets.html.php');
            break;
              
        case '/edit':
            $app->checkApiAuthorization('pentester');
            $result = $app->useController('AuthenticatorController', 'handleEditRoute');
            if ($result['status'] == 200) {
                $app->handleAuthenticatedRoute($result['data']['view'], $result['data']['role']);
            }
            break;

        case '/admin/customer':
            $app->handleAuthenticatedRoute('adminCustomer.html.php', 'admin');
            break;

        case '/admin/pentester':
            $app->handleAuthenticatedRoute('adminPentester.html.php', 'admin');
            break;

        // API Routes
        case '/api/login':
            $result = $app->useController("AuthenticatorController", "login");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/logout':
            $result = $app->useController("AuthenticatorController", "logout");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/check-login':
            $result = $app->useController("AuthenticatorController", "isLoggedIn");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/get-all-customers':
            $app->handleApiRoute(['admin', 'pentester'], "AdminDashboardController", "getCustomers");
            break;

        case '/api/get-all-employees':
            $app->handleApiRoute(['admin', 'pentester'], "AdminDashboardController", "getEmployees");
            break;

        case '/api/get-all-admins':
            $app->handleApiRoute('admin', "AdminDashboardController", "getAdmins");
            break;

        case '/api/get-all-customer-tests':
            $app->handleApiRoute(['admin', 'customer', 'pentester'], "IndexController", "getCustomersTests");
            break;

        case '/api/get-all-employee-tests':
            $app->handleApiRoute(['admin', 'pentester'], "EmployeeDashboardController", "getEmployeeTests");
            break;

        case '/api/update-test-completion':
            $app->handleApiRoute('pentester', "EmployeeDashboardController", "updateTestCompletion");
            break;

        case '/api/get-all-targets':
            $app->checkApiAuthorization(['pentester', 'customer']);
            $result = $app->useController("TargetController", "handleApiTargets");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/get-all-vulnerabilities':
            $app->checkApiAuthorization(['pentester', 'customer']);
            $target_id = isset($_GET['target_id']) ? (int)$_GET['target_id'] : null;
            $result = $app->useController("VulnerabilityController", "getVulnerabilities", [$target_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/get-test':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            $app->handleApiRoute(['pentester', 'customer'], "DataController", "getTest", [$data['test_id']]);
            break;

        case '/api/get-target':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            $app->handleApiRoute('pentester', "DataController", "getTarget", [$data['target_id']]);
            break;

        case '/api/get-vulnerability':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            $app->handleApiRoute('pentester', "DataController", "getVulnerability", [$data['vulnerability_id']]);
            break;

        case '/api/get-customer-email':
            $app->checkApiAuthorization(['pentester', 'admin']);
            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
            $result = $app->useController("DataController", "getCustomerEmail", [$customer_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/customer-tests':
            $app->checkApiAuthorization(['pentester', 'admin']);
            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
            $result = $app->useController("AdminDashboardController", "getCustomerTests", [$customer_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/customer-details':
            $app->checkApiAuthorization(['pentester', 'admin']);
            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
            $result = $app->useController("AdminDashboardController", "getCustomerDetails", [$customer_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/customer-targets':
            $app->checkApiAuthorization(['pentester', 'customer', 'admin']);
            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
            if (!$customer_id) {
                throw new HTTPException('Customer ID is required', 400);
            }
            $result = $app->useController("AdminDashboardController", "getCustomerTargets", [$customer_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/customer-vulnerabilities':
            $app->checkApiAuthorization(['pentester', 'customer', 'admin']);
            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
            if (!$customer_id) {
                throw new HTTPException('Customer ID is required', 400);
            }
            $result = $app->useController("AdminDashboardController", "getCustomerVulnerabilities", [$customer_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/pentester-details':
            $app->checkApiAuthorization(['pentester', 'admin']);
            $pentester_id = isset($_GET['pentester_id']) ? (int)$_GET['pentester_id'] : null;
            $result = $app->useController("AdminDashboardController", "getPentesterDetails", [$pentester_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/pentester-tests':
            $app->checkApiAuthorization(['pentester', 'admin']);
            $pentester_id = isset($_GET['pentester_id']) ? (int)$_GET['pentester_id'] : null;
            $result = $app->useController("AdminDashboardController", "getPentesterTests", [$pentester_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/pentester-targets':
            $app->checkApiAuthorization(['pentester', 'admin']);
            $pentester_id = isset($_GET['pentester_id']) ? (int)$_GET['pentester_id'] : null;
            $result = $app->useController("AdminDashboardController", "getPentesterTargets", [$pentester_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/pentester-vulnerabilities':
            $app->checkApiAuthorization(['pentester', 'admin']);
            $pentester_id = isset($_GET['pentester_id']) ? (int)$_GET['pentester_id'] : null;
            $result = $app->useController("AdminDashboardController", "getPentesterVulnerabilities", [$pentester_id]);
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/check-access':
            $app->checkApiAuthentication();
            $result = $app->useController("AuthenticatorController", "doesUserHaveAccess");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/create-account':
            $data = $app->decodeBody();
            $app->handleApiRoute('admin', "AdminDashboardController", "createNewAccount", [$data]);
            break;

        case '/create-test':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            Logger::write('info', 'Creating test with data: ' . json_encode($data));
            $app->handleApiRoute('pentester', "EmployeeDashboardController", "createTest", [$data['customer_id']]);
            break;

        case '/update-test':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            Logger::write('info', 'Updating test with data: ' . json_encode($data));
            $app->handleApiRoute('pentester', "DataController", "updateTest", [$data['test_id'], $data['test_name'], $data['test_description']]);
            break;

        case '/update-target':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            Logger::write('info', 'Updating target with data: ' . json_encode($data));
            $app->handleApiRoute('pentester', "DataController", "updateTarget", [$data['target_id'], $data['target_name'], $data['target_description']]);
            break;

        case '/update-vulnerability':
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            Logger::write('info', 'Updating vulnerability with data: ' . json_encode($data));
            $app->handleApiRoute('pentester', "DataController", "updateVulnerability", [
                $data['vulnerability_id'],
                $data['affected_entity'],
                $data['identifier'],
                $data['risk_statement'],
                $data['affected_component'],    
                $data['residual_risk'],
                $data['classification'],
                $data['identified_controls'],
                $data['cvss_score'],
                $data['likelihood'],
                $data['cvssv3_code'],
                $data['location'],
                $data['vulnerabilities_description'],
                $data['reproduction_steps'],
                $data['impact'],
                $data['remediation_difficulty'],
                $data['recommendations'],
                $data['recommended_reading'],
                $data['response']
            ]);
            break;

        case '/api/update-vulnerability-solved':
            $app->handleApiRoute('customer', "DataController", "updateVulnerabilitySolved");
            break;

        case '/api/change-password':
            $app->checkApiAuthentication();
            $result = $app->useController("AuthenticatorController", "changePassword");
            $app->sendJsonResponse($result['data'], $result['status']);
            break;

        case '/api/add-target':
            $app->checkApiAuthentication();
            $test_id = isset($_GET['test_id']) ? (int)$_GET['test_id'] : null;
            if (!$test_id) {
                throw new HTTPException('Test ID is required', 400);
            }
            $app->handleApiRoute('pentester', "TargetController", "addTarget", [$test_id]);
            break;

        case '/api/add-vulnerability':
            $app->checkApiAuthentication();
            $target_id = isset($_GET['target_id']) ? (int)$_GET['target_id'] : null;
            if (!$target_id) {
                throw new HTTPException('Target ID is required', 400);
            }
            $app->handleApiRoute('pentester', "VulnerabilityController", "addVulnerability", [$target_id]);
            break;
        
        case '/api/delete':
            $app->checkApiAuthentication();
            if (isset($_GET['test_id'])) {
                $test_id = (int)$_GET['test_id'];
                $app->handleApiRoute('pentester', "EmployeeDashboardController", "deleteTest", [$test_id]);
            } else if (isset($_GET['target_id'])) {
                $target_id = (int)$_GET['target_id'];
                $app->handleApiRoute('pentester', "TargetController", "deleteTarget", [$target_id]);
            } else if (isset($_GET['vulnerability_id'])) {
                $vulnerability_id = (int)$_GET['vulnerability_id'];
                $app->handleApiRoute('pentester', "VulnerabilityController", "deleteVulnerability", [$vulnerability_id]);
            } else if (isset($_GET['user_id'])) {
                $user_id = (int)$_GET['user_id'];
                $app->handleApiRoute('admin', "AdminDashboardController", "deleteUser", [$user_id]);
            } else {
                throw new HTTPException('Invalid request: test_id, target_id or vulnerability_id is required', 400);
            }
            break;

        // Routes for styling and scripts
        case '/js/bootstrap.js':
            $app->handleStaticAsset('/node_modules/bootstrap/dist/js/bootstrap.bundle.js', 'application/javascript');
            break;

        case '/css/bootstrap.css':
            $app->handleStaticAsset('/node_modules/bootstrap/dist/css/bootstrap.css', 'text/css');
            break;

        default:
            throw new HTTPException('Route not found', 404);
    }
} catch (HTTPException $e) {
    Logger::write('error', 'HTTP error: ' . $e->getMessage() . ' - ' . $uri . ' - Line: ' . $e->getLine() . ' - File: ' . basename($e->getFile()));
    $c = new ErrorController();
    $c($e);
} catch (\Throwable $e) {
    Logger::write('error', 'Unhandled error: ' . $e->getMessage());
    header('Content-Type: text/html');
    http_response_code(500);
    include DIR_VIEWS . 'error.html.php';
}